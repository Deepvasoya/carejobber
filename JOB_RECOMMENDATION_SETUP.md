# Job Recommendation Email System - Setup Guide

## ✅ Feature Status: IMPLEMENTED & WORKING

The job recommendation email system is already fully implemented in your application. Here's how it works:

---

## 📧 How It Works

### 1. **Job Seeker Recommendations** (When a new job is posted)
- **Trigger**: When an employer posts a new job
- **Event**: `JobPosted` event is fired
- **Listener**: `SendJobRecommendationsListener`
- **Matching Logic**:
  - Finds active, verified job seekers
  - Matches by `functional_area_id` (e.g., Registered Nurse)
  - Optionally matches by `career_level_id`
  - Checks user notification preferences
- **Email**: Sends job details WITHOUT contact information
- **Template**: `job-recommendation-jobseeker`

### 2. **Employer Recommendations** (When a job seeker registers)
- **Trigger**: When a new job seeker completes registration
- **Event**: `UserRegistered` event is fired
- **Listener**: `SendCandidateRecommendationsListener`
- **Matching Logic**:
  - Finds active jobs posted in last 30 days
  - Matches by `functional_area_id`
  - Optionally matches by `career_level_id`
  - Limits to 5 most recent matching jobs
- **Email**: Sends candidate profile WITHOUT contact details
- **Template**: `candidate-recommendation-employer`

---

## 🔧 Current Configuration

### Queue Driver
Your `.env` file shows:
```env
QUEUE_DRIVER=sync
```

**⚠️ IMPORTANT**: With `sync` driver, emails are sent immediately (synchronously), which can slow down the registration/job posting process.

---

## 🚀 Server Setup Instructions

### Option 1: Database Queue (Recommended for Production)

#### Step 1: Update .env
```bash
QUEUE_DRIVER=database
```

#### Step 2: Verify queue_jobs table exists
```bash
php artisan migrate
```

#### Step 3: Setup Queue Worker as Systemd Service

Create file: `/etc/systemd/system/carejobber-queue.service`

```ini
[Unit]
Description=CareJobber Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=5
WorkingDirectory=/path/to/your/carejobber
ExecStart=/usr/bin/php /path/to/your/carejobber/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

**Replace `/path/to/your/carejobber` with your actual project path**

#### Step 4: Enable and Start Service
```bash
sudo systemctl daemon-reload
sudo systemctl enable carejobber-queue
sudo systemctl start carejobber-queue
```

#### Step 5: Check Status
```bash
sudo systemctl status carejobber-queue
```

#### Step 6: View Logs
```bash
sudo journalctl -u carejobber-queue -f
```

---

### Option 2: Cron Job (Alternative)

#### Step 1: Update .env
```bash
QUEUE_DRIVER=database
```

#### Step 2: Add to Crontab
```bash
crontab -e
```

Add this line:
```cron
* * * * * cd /path/to/your/carejobber && php artisan schedule:run >> /dev/null 2>&1
```

#### Step 3: Add Queue Worker to Schedule

Edit `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Process queue every minute
    $schedule->command('queue:work --stop-when-empty')
             ->everyMinute()
             ->withoutOverlapping();
}
```

---

### Option 3: Keep Sync (For Testing Only)

If you want to test locally with immediate email sending:

```bash
QUEUE_DRIVER=sync
```

**Note**: This will send emails immediately but may slow down the application.

---

## 🧪 Testing the Feature

### Test Job Seeker Recommendations

1. **Create a job seeker account** with:
   - Functional Area: "Registered Nurse"
   - Career Level: "Entry Level"
   - Make sure account is verified and active

2. **Post a new job** as employer with:
   - Functional Area: "Registered Nurse"
   - Career Level: "Entry Level"

3. **Check**:
   - If using `database` queue: Check `queue_jobs` table
   - Run: `php artisan queue:work` to process
   - Check job seeker's email inbox

### Test Employer Recommendations

1. **Post a job** as employer with:
   - Functional Area: "Registered Nurse"
   - Career Level: "Entry Level"

2. **Register a new job seeker** with:
   - Functional Area: "Registered Nurse"
   - Career Level: "Entry Level"

3. **Check**:
   - If using `database` queue: Check `queue_jobs` table
   - Run: `php artisan queue:work` to process
   - Check employer's email inbox

---

## 📊 Monitoring

### Check Queue Jobs
```bash
# View pending jobs
php artisan queue:monitor

# Or check database directly
mysql -u root -p
USE carejobber_live;
SELECT * FROM queue_jobs;
```

### Check Failed Jobs
```bash
# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

Look for:
- `Job recommendation sent`
- `Candidate recommendation sent`
- `Failed to send job recommendation`

---

## 🔐 Privacy Features (Already Implemented)

✅ **No contact details in emails**
- Job seeker emails: Show job title, company name, location (NO company email/phone)
- Employer emails: Show candidate name, functional area, career level (NO candidate email/phone)

✅ **User preferences respected**
- Checks `user_notification_preferences` table
- Users can opt-out of job match notifications

✅ **Smart filtering**
- Only active, verified users receive emails
- Only active, non-draft jobs trigger emails
- Employer recommendations limited to jobs posted in last 30 days
- Maximum 5 recommendations per candidate registration

---

## 📝 Email Templates

Email templates are stored in database table `email_templates`:

1. **job-recommendation-jobseeker**: Sent to job seekers about new jobs
2. **candidate-recommendation-employer**: Sent to employers about new candidates

You can customize these templates from the admin panel.

---

## ⚡ Performance Tips

1. **Use database queue** instead of sync in production
2. **Run queue worker as systemd service** for reliability
3. **Monitor queue_jobs table** size regularly
4. **Set up queue monitoring** alerts
5. **Use supervisor** for better process management (optional)

---

## 🐛 Troubleshooting

### Emails not sending?

1. Check queue driver:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. Check if jobs are queued:
   ```sql
   SELECT * FROM queue_jobs;
   ```

3. Process queue manually:
   ```bash
   php artisan queue:work --once
   ```

4. Check mail configuration:
   - Verify `USE_DB_MAIL_CONFIG` in .env
   - Test with Mailhog locally
   - Check `storage/logs/laravel.log`

### Queue worker stopped?

```bash
sudo systemctl restart carejobber-queue
sudo systemctl status carejobber-queue
```

---

## 📌 Summary

**Current Status**: ✅ Feature is fully implemented and working

**To Enable on Server**:
1. Set `QUEUE_DRIVER=database` in .env
2. Run migrations
3. Setup systemd service OR cron job
4. Start queue worker
5. Test with real job posting and registration

**No additional code needed** - the feature is ready to use!
