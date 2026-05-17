const puppeteer = require('puppeteer');

async function scrapeJobList(page, url) {
  console.log('Fetching job list...');
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });
  await page.waitForSelector('.job_list_row', { timeout: 30000 }).catch(() => {});
  await new Promise(r => setTimeout(r, 3000));

  async function scrapeCurrentPage() {
    return await page.evaluate(() => {
      const rows = document.querySelectorAll('.job_list_row');
      const results = [];
      rows.forEach(row => {
        const link = row.querySelector('a.job_link');
        if (!link) return;
        const url = link.href;
        if (!url || url.includes('/jobs/search')) return;
        results.push({
          title: (link.innerText || link.textContent).replace(/\s+/g, ' ').trim(),
          url,
          location: (row.querySelector('.location')?.innerText || '').replace(/\s+/g, ' ').trim() || 'N/A',
          category: (row.querySelector('.category')?.innerText || '').replace(/\s+/g, ' ').trim() || 'N/A',
          isNew: !!row.querySelector('.jNewBadge') || ((row.querySelector('.flg_hldr') || row).innerText || '').includes('NEW'),
        });
      });
      return results;
    });
  }

  const allJobs = new Map();
  let jobs = await scrapeCurrentPage();
  jobs.forEach(j => allJobs.set(j.url, j));
  console.log(`  Page 1: ${jobs.length} jobs`);

  const totalPages = await page.evaluate(() => {
    const links = document.querySelectorAll('.pagination_holder a');
    let max = 1;
    links.forEach(a => {
      const m = a.href.match(/#page(\d+)/);
      if (m) { const n = parseInt(m[1]); if (n > max) max = n; }
    });
    return max;
  });
  console.log(`  Total pages: ${totalPages}`);

  for (let p = 2; p <= totalPages; p++) {
    await page.evaluate((pn) => {
      const links = document.querySelectorAll('.pagination_holder a');
      for (const a of links) {
        if (a.href && a.href.includes(`#page${pn}`)) { a.click(); return; }
      }
      const fallback = document.querySelector(`a[href*="#page${pn}"]`);
      if (fallback) fallback.click();
    }, p);
    await new Promise(r => setTimeout(r, 3000));
    await page.waitForSelector('.job_list_row', { timeout: 10000 }).catch(() => {});
    const j = await scrapeCurrentPage();
    let n = 0;
    j.forEach(jj => { if (!allJobs.has(jj.url)) { allJobs.set(jj.url, jj); n++; } });
    console.log(`  Page ${p}: ${j.length} rows, ${n} new. Total: ${allJobs.size}`);
  }

  return Array.from(allJobs.values());
}

async function scrapeJobDetail(page, jobUrl) {
  try {
    await page.goto(jobUrl, { waitUntil: 'networkidle2', timeout: 30000 });
    await new Promise(r => setTimeout(r, 2000));

    return await page.evaluate(() => {
      const txt = (el) => (el?.innerText || '').replace(/\s+/g, ' ').trim();

      // Parse labeled fields from the detail page
      const labels = document.querySelectorAll('h1, h2, h3, h4, h5, strong, b, dt, th, .detail_label, [class*="label"]');
      const fields = {};

      labels.forEach(el => {
        const label = (el.innerText || '').trim();
        if (!label || label.length > 50) return;

        let value = '';
        // Try next sibling
        let next = el.nextElementSibling;
        if (next && ['P', 'SPAN', 'DIV', 'DD', 'TD', 'LI'].includes(next.tagName)) {
          value = txt(next);
        }
        // Try next sibling of parent
        if (!value && el.parentElement) {
          const ps = el.parentElement.nextElementSibling;
          if (ps) value = txt(ps);
        }
        if (label.endsWith(':') && value) {
          fields[label.replace(/:$/, '')] = value;
        }
      });

      // Specific known fields via regex on full page text
      const bodyText = document.body.innerText;

      const extract = (regex) => {
        const m = bodyText.match(regex);
        return m ? m[1].trim() : '';
      };

      // Parse structured fields from the capsule/top section
      const reqMatch = bodyText.match(/Requisition\s*#:\s*(\S+)/);
      const dateMatch = bodyText.match(/📅\s*([^\n]+)/);
      const locMatch = bodyText.match(/📍?\s*([^\n]+?)(?:\s*📁|\s*$)/);

      // Get the "Your Opportunity" / "Description" section
      const oppMatch = bodyText.match(/Your\s*Opportunity:\s*([^]*?)(?=\n\s*(?:Description|Classification|Union|Required Qualifications|$))/i);
      const descMatch = bodyText.match(/Description:\s*([^]*?)(?=\n\s*(?:Classification|Union|Unit|Primary Location|Location Details|Multi-Site|FTE|Posting End|Required Qualifications|$))/i);
      const qualMatch = bodyText.match(/Required Qualifications:\s*([^]*?)(?=\n\s*(?:Additional Required|Preferred Qualifications|Security Screening|$))/i);

      return {
        requisitionId: reqMatch?.[1] || '',
        postedDate: dateMatch?.[1]?.replace(/^\d{1,2}\s+day(s)?\s+ago/, '').trim() || dateMatch?.[1]?.trim() || '',
        location: locMatch?.[1]?.trim() || '',
        opportunity: oppMatch?.[1]?.trim() || '',
        description: descMatch?.[1]?.trim() || '',
        requiredQualifications: qualMatch?.[1]?.trim() || '',
        // Additional structured fields
        classification: extract(/Classification:\s*([^\n]+)/),
        union: extract(/Union:\s*([^\n]+)/),
        unitAndProgram: extract(/Unit and Program:\s*([^\n]+)/),
        primaryLocation: extract(/Primary Location:\s*([^\n]+)/),
        locationDetails: extract(/Location Details:\s*([^\n]+)/),
        multiSite: extract(/Multi-Site:\s*([^\n]+)/),
        fte: extract(/FTE:\s*([^\n]+)/),
        postingEndDate: extract(/Posting End Date:\s*([^\n]+)/),
        employeeClass: extract(/Employee Class:\s*([^\n]+)/),
        dateAvailable: extract(/Date Available:\s*([^\n]+)/),
        hoursPerShift: extract(/Hours per Shift:\s*([^\n]+)/),
        shiftPattern: extract(/Shift Pattern:\s*([^\n]+)/),
        daysOff: extract(/Days Off:\s*([^\n]+)/),
        minSalary: extract(/Minimum Salary:\s*([^\n]+)/),
        maxSalary: extract(/Maximum Salary:\s*([^\n]+)/),
        vehicleRequirement: extract(/Vehicle Requirement:\s*([^\n]+)/),
        additionalQualifications: extract(/Additional Required Qualifications:\s*([^]*?)(?=\n\s*(?:Preferred Qualifications|Security Screening|$))/),
        preferredQualifications: extract(/Preferred Qualifications:\s*([^]*?)(?=\n\s*(?:Security Screening|$))/),
      };
    });
  } catch (e) {
    console.log(`  Error fetching detail: ${jobUrl.substring(0, 80)} - ${e.message}`);
    return {};
  }
}

(async () => {
  const browser = await puppeteer.launch({ headless: 'new' });
  const page = await browser.newPage();
  await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

  const listUrl = process.argv[2] || 'https://careers.covenanthealth.ca/jobs/search/139924161';
  const outputFile = process.argv[3] || '';
  const maxDetails = parseInt(process.argv[4]) || 0; // 0 = all

  // Phase 1: Scrape the job list
  const jobs = await scrapeJobList(page, listUrl);
  console.log(`\n=== PHASE 1 DONE: ${jobs.length} jobs from search ===\n`);

  // Phase 2: Scrape each job's detail page
  console.log('=== PHASE 2: Fetching job details ===');
  const toFetch = maxDetails > 0 ? jobs.slice(0, maxDetails) : jobs;

  for (let i = 0; i < toFetch.length; i++) {
    const job = toFetch[i];
    process.stdout.write(`  [${i + 1}/${toFetch.length}] ${job.title.substring(0, 50)}... `);
    const detail = await scrapeJobDetail(page, job.url);
    Object.assign(job, detail);
    console.log(`✓ ${job.requisitionId || ''}`);
  }

  // Phase 3: Output
  console.log(`\n\n=== FINAL: ${jobs.length} jobs scraped ===`);
  const output = JSON.stringify(jobs, null, 2);

  if (outputFile) {
    require('fs').writeFileSync(outputFile, output);
    console.log(`Saved to: ${outputFile}`);
  } else {
    console.log(output);
  }

  // Summary
  const byCategory = {};
  jobs.forEach(j => { byCategory[j.category] = (byCategory[j.category] || 0) + 1; });
  console.log('\n=== BY CATEGORY ===');
  Object.entries(byCategory).sort((a, b) => b[1] - a[1]).forEach(([c, n]) => console.log(`  ${c}: ${n}`));

  await browser.close();
})();
