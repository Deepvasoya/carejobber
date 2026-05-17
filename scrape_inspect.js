const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new' });
  const page = await browser.newPage();
  await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

  // Visit a specific job detail page to see its structure
  const jobUrl = 'https://careers.covenanthealth.ca/jobs/health-care-aide-574265';
  console.log('Navigating to job detail:', jobUrl);
  await page.goto(jobUrl, { waitUntil: 'networkidle2', timeout: 60000 });
  await new Promise(r => setTimeout(r, 3000));

  const detail = await page.evaluate(() => {
    const info = {};

    // Try to get job-specific detail containers
    const containers = document.querySelectorAll('.jDetailPanel, .job_detail, .detail_holder, [class*="detail"], .jJobDetailSection, #job_detail, .job-description, .posting');
    containers.forEach(c => {
      const cls = c.className;
      info[cls || 'unknown'] = (c.innerText || '').replace(/\s+/g, ' ').trim().substring(0, 500);
    });

    // Also check for all major content sections
    const sections = {};
    document.querySelectorAll('h1, h2, h3, h4, h5, strong, b, dt, th').forEach(el => {
      const text = (el.innerText || '').trim();
      if (text && text.length < 100) {
        let value = '';
        let next = el.nextElementSibling;
        if (next && ['P', 'SPAN', 'DIV', 'DD', 'TD'].includes(next.tagName)) {
          value = (next.innerText || '').replace(/\s+/g, ' ').trim().substring(0, 200);
        }
        // Also check parent's next sibling
        if (!value && el.parentElement) {
          const parent = el.parentElement;
          const parentNext = parent.nextElementSibling;
          if (parentNext) {
            value = (parentNext.innerText || '').replace(/\s+/g, ' ').trim().substring(0, 200);
          }
        }
        sections[text] = value;
      }
    });

    info['__sections'] = sections;

    // Get page title
    info['__title'] = document.title;
    info['__bodyClass'] = document.body.className;
    info['__url'] = window.location.href;

    // Get all meta tags
    const metas = {};
    document.querySelectorAll('meta').forEach(m => {
      const name = m.getAttribute('name') || m.getAttribute('property') || '';
      const content = m.getAttribute('content') || '';
      if (name && content) metas[name] = content.substring(0, 200);
    });
    info['__metas'] = metas;

    return info;
  });

  console.log('\n=== JOB DETAIL ===');
  console.log(JSON.stringify(detail, null, 2));

  await browser.close();
})();
