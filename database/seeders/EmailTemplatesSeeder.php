<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\EmailTemplate;

class EmailTemplatesSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            // User Registration
            [
                'slug' => 'user-registered',
                'name' => 'User Registration Notification',
                'subject' => 'New Job Seeker Registration - {USER_NAME}',
                'body' => '<p>Dear Admin,</p><p>Job Seeker with name "{USER_NAME}" has been registered on "{SITE_NAME}"</p><p><strong>Email:</strong> {USER_EMAIL}</p><p><strong>Front link:</strong> <a href="{USER_LINK}">{USER_LINK}</a></p><p><strong>Admin link:</strong> <a href="{USER_ADMIN_LINK}">{USER_ADMIN_LINK}</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{USER_NAME}' => 'User full name',
                    '{USER_EMAIL}' => 'User email address',
                    '{USER_LINK}' => 'User profile link',
                    '{USER_ADMIN_LINK}' => 'Admin edit user link'
                ]),
                'category' => 'user'
            ],
            
            // Company Registration
            [
                'slug' => 'company-registered',
                'name' => 'Company Registration Notification',
                'subject' => 'New Employer Registration - {COMPANY_NAME}',
                'body' => '<p>Dear Admin,</p><p>Employer/Company with name "{COMPANY_NAME}" has been registered on "{SITE_NAME}"</p><p><strong>Email:</strong> {COMPANY_EMAIL}</p><p><strong>Front link:</strong> <a href="{COMPANY_LINK}">{COMPANY_LINK}</a></p><p><strong>Admin link:</strong> <a href="{COMPANY_ADMIN_LINK}">{COMPANY_ADMIN_LINK}</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{COMPANY_NAME}' => 'Company name',
                    '{COMPANY_EMAIL}' => 'Company email',
                    '{COMPANY_LINK}' => 'Company profile link',
                    '{COMPANY_ADMIN_LINK}' => 'Admin edit company link'
                ]),
                'category' => 'company'
            ],

            // Job Posted (Admin Notification)
            [
                'slug' => 'job-posted-admin',
                'name' => 'Job Posted - Admin Notification',
                'subject' => 'New Job Posted - {JOB_TITLE}',
                'body' => '<p>Dear Super Admin,</p><p>An employer has submitted a job vacancy listing for approval in the "Jobs" section on {SITE_NAME}. Here are the details:</p><p>Employer/Company with name "{COMPANY_NAME}" has posted new job on "{SITE_NAME}"</p><p><strong>Job Title:</strong> {JOB_TITLE}</p><p><strong>Job Link:</strong> <a href="{JOB_ADMIN_LINK}">{JOB_ADMIN_LINK}</a></p><p>Please review the submission and take appropriate action.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{COMPANY_NAME}' => 'Company name',
                    '{JOB_TITLE}' => 'Job title',
                    '{JOB_LINK}' => 'Job detail page link',
                    '{JOB_ADMIN_LINK}' => 'Admin edit job link'
                ]),
                'category' => 'job'
            ],

            // Job Posted (Company Notification)
            [
                'slug' => 'job-posted-company',
                'name' => 'Job Posted - Company Notification',
                'subject' => 'Job Vacancy Submission Pending Approval',
                'body' => '<p>Dear {COMPANY_NAME},</p><p>Thank you for submitting your job vacancy advert on {SITE_NAME}. We have received your submission and it is currently pending approval by our super admin team.</p><p>We will notify you once your job advert has been <strong>reviewed</strong> and <strong>approved</strong>. In the meantime, if you have any questions or need further assistance, please do not hesitate to contact us.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{COMPANY_NAME}' => 'Company name',
                    '{JOB_TITLE}' => 'Job title',
                    '{JOB_LINK}' => 'Job detail page link'
                ]),
                'category' => 'job'
            ],

            // Job Approved
            [
                'slug' => 'job-approved',
                'name' => 'Job Approved Notification',
                'subject' => 'Your Job Vacancy Advert Has Been Approved!',
                'body' => '<p>Dear {COMPANY_NAME},</p><p>We are pleased to inform you that your job vacancy advert has been approved and is now live on {SITE_NAME}. Thank you for choosing our platform to reach potential candidates.</p><p>If you have any questions or need further assistance, please feel free to contact us.</p><p><strong>Job Link:</strong><br><a href="{JOB_LINK}" style="display: inline-block; background: rgb(87, 64, 189); color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">Click here to View Job</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{COMPANY_NAME}' => 'Company name',
                    '{JOB_TITLE}' => 'Job title',
                    '{JOB_LINK}' => 'Job detail page link'
                ]),
                'category' => 'job'
            ],

            // Job Application - Company Notification
            [
                'slug' => 'job-applied-company',
                'name' => 'Job Application Received - Company',
                'subject' => 'New Job Application Received',
                'body' => '<p>Dear {COMPANY_NAME},</p><p>We are pleased to inform you that {USER_NAME} has submitted an application for your job vacancy posted on {SITE_NAME}. You can review their resume/CV by following:</p><p><strong>Candidate Profile:</strong> <a href="{USER_LINK}">{USER_LINK}</a></p><p><strong>Job Link:</strong> <a href="{JOB_LINK}">{JOB_LINK}</a></p><p>If you have any questions or need further assistance, please feel free to contact us.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{COMPANY_NAME}' => 'Company name',
                    '{USER_NAME}' => 'Applicant name',
                    '{USER_LINK}' => 'Applicant profile link',
                    '{JOB_TITLE}' => 'Job title',
                    '{JOB_LINK}' => 'Job detail page link'
                ]),
                'category' => 'application'
            ],

            // Job Application - Job Seeker Notification
            [
                'slug' => 'job-applied-jobseeker',
                'name' => 'Job Application Received - Job Seeker',
                'subject' => 'Application Received for {JOB_TITLE}',
                'body' => '<p>Dear {USER_NAME},</p><p>Thank you for applying to the <strong>{JOB_TITLE}</strong> position at <strong>{COMPANY_NAME}</strong> on {SITE_NAME}. We have received your application and it is currently under review.</p><p>You can view the job post and visit the employer\'s profile using the links below.</p><p>If you have any questions or need further assistance, please feel free to contact us.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{USER_NAME}' => 'Applicant name',
                    '{COMPANY_NAME}' => 'Company name',
                    '{COMPANY_LINK}' => 'Company profile link',
                    '{JOB_TITLE}' => 'Job title',
                    '{JOB_LINK}' => 'Job detail page link'
                ]),
                'category' => 'application'
            ],

            // Job Application Status Update
            [
                'slug' => 'job-application-status',
                'name' => 'Job Application Status Update',
                'subject' => 'Application Status Update - {JOB_TITLE}',
                'body' => '<p>Dear {USER_NAME},</p><p>This is to inform you about the status update of your application for the <strong>{JOB_TITLE}</strong> position at <strong>{COMPANY_NAME}</strong>.</p><p><strong>Current Status:</strong> {APPLICATION_STATUS}</p><p>{STATUS_MESSAGE}</p><p><strong>Job Link:</strong> <a href="{JOB_LINK}">{JOB_LINK}</a></p><p><strong>Company Profile:</strong> <a href="{COMPANY_LINK}">{COMPANY_LINK}</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{USER_NAME}' => 'Applicant name',
                    '{COMPANY_NAME}' => 'Company name',
                    '{COMPANY_LINK}' => 'Company profile link',
                    '{JOB_TITLE}' => 'Job title',
                    '{JOB_LINK}' => 'Job detail page link',
                    '{APPLICATION_STATUS}' => 'Application status (Approved/Rejected/Shortlisted)',
                    '{STATUS_MESSAGE}' => 'Detailed status message'
                ]),
                'category' => 'application'
            ],

            // Contact Form
            [
                'slug' => 'contact-form',
                'name' => 'Contact Form Submission',
                'subject' => 'New Contact Form Submission - {SUBJECT}',
                'body' => '<p>Dear Admin,</p><p>Following email has been received from contact form:</p><p><strong>Full Name:</strong> {FULL_NAME}</p><p><strong>Email:</strong> {EMAIL}</p><p><strong>Phone:</strong> {PHONE}</p><p><strong>Subject:</strong> {SUBJECT}</p><p><strong>Message:</strong><br>{MESSAGE}</p><p>You can respond to "{FULL_NAME}" by replying to this email.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{FULL_NAME}' => 'Sender full name',
                    '{EMAIL}' => 'Sender email',
                    '{PHONE}' => 'Sender phone',
                    '{SUBJECT}' => 'Message subject',
                    '{MESSAGE}' => 'Message content'
                ]),
                'category' => 'contact'
            ],

            // Chat Message Notification
            [
                'slug' => 'chat-message',
                'name' => 'Chat Message Notification',
                'subject' => 'New Message from {SENDER_NAME}',
                'body' => '<p>Dear {RECIPIENT_NAME},</p><p>You have received a new message from {SENDER_NAME} on {SITE_NAME}.</p><p><strong>Message:</strong> {MESSAGE_PREVIEW}</p><p><a href="{CHAT_URL}" style="display: inline-block; background: #f25a55; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">View Message</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{SENDER_NAME}' => 'Message sender name',
                    '{RECIPIENT_NAME}' => 'Message recipient name',
                    '{MESSAGE_PREVIEW}' => 'Message preview/content',
                    '{MESSAGE_TYPE}' => 'Message type (text/image/file)',
                    '{CHAT_URL}' => 'Chat conversation URL'
                ]),
                'category' => 'messaging'
            ],

            // Resume Posted Notification
            [
                'slug' => 'resume-posted',
                'name' => 'Resume Posted Notification',
                'subject' => 'New Resume Available in Your Industry',
                'body' => '<p>Hello <strong>{COMPANY_NAME}</strong>,</p><p>A new candidate has posted/updated their resume in your industry on {SITE_NAME}.</p><div style="background-color: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0;"><p><strong>Candidate Information:</strong></p><ul><li><strong>Name:</strong> {USER_NAME}</li><li><strong>Functional Area:</strong> {FUNCTIONAL_AREA}</li><li><strong>Career Level:</strong> {CAREER_LEVEL}</li><li><strong>Location:</strong> {LOCATION}</li></ul></div><p style="text-align: center;"><a href="{PROFILE_URL}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px;">View Resume</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{COMPANY_NAME}' => 'Company name',
                    '{USER_NAME}' => 'Job seeker name',
                    '{FUNCTIONAL_AREA}' => 'Functional area',
                    '{CAREER_LEVEL}' => 'Career level',
                    '{LOCATION}' => 'Location',
                    '{PROFILE_URL}' => 'Profile URL'
                ]),
                'category' => 'notification'
            ],

            // Incomplete Profile Reminder
            [
                'slug' => 'incomplete-profile',
                'name' => 'Incomplete Profile Reminder',
                'subject' => 'Complete Your Profile - {SITE_NAME}',
                'body' => '<p>Hello <strong>{USER_NAME}</strong>,</p><p>We noticed that your profile on {SITE_NAME} is not yet complete.</p><p>A complete profile helps employers find you and increases your chances of getting hired!</p><div style="background-color: #f8f9fa; padding: 20px; margin: 20px 0;"><p><strong>Benefits of completing your profile:</strong></p><ul><li>Get discovered by more employers</li><li>Increase your profile visibility</li><li>Receive better job matches</li><li>Stand out from other candidates</li></ul></div><p style="text-align: center;"><a href="{PROFILE_URL}" style="display: inline-block; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px;">Complete My Profile Now</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{USER_NAME}' => 'User name',
                    '{PROFILE_URL}' => 'Profile edit URL',
                    '{MISSING_FIELDS}' => 'List of missing fields'
                ]),
                'category' => 'notification'
            ],

            // Job Alerts
            [
                'slug' => 'job-alerts',
                'name' => 'Job Alerts Email',
                'subject' => '{SUBJECT}',
                'body' => '<p>{SUBJECT}</p>{JOB_LIST}<p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{SUBJECT}' => 'Email subject',
                    '{JOB_LIST}' => 'List of jobs matching criteria',
                    '{DISABLE_LINK}' => 'Link to disable alerts'
                ]),
                'category' => 'notification'
            ],

            // Email to Friend
            [
                'slug' => 'email-to-friend',
                'name' => 'Email to Friend',
                'subject' => 'Your friend {YOUR_NAME} has shared a link with you',
                'body' => '<p>Dear {FRIEND_NAME},</p><p>{YOUR_NAME} has shared a link with you:</p><p><a href="{JOB_URL}" style="display: inline-block; background: #f25a55; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">{JOB_URL}</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{YOUR_NAME}' => 'Sender name',
                    '{YOUR_EMAIL}' => 'Sender email',
                    '{FRIEND_NAME}' => 'Friend name',
                    '{FRIEND_EMAIL}' => 'Friend email',
                    '{JOB_URL}' => 'Shared job URL'
                ]),
                'category' => 'sharing'
            ],

            // Report Abuse
            [
                'slug' => 'report-abuse',
                'name' => 'Report Abuse Notification',
                'subject' => '{YOUR_NAME} has reported a link',
                'body' => '<p>Dear Admin,</p><p>"{YOUR_NAME}" has reported a "{SITE_NAME}" link:</p><p><a href="{REPORTED_URL}" style="display: inline-block; background: #f25a55; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">{REPORTED_URL}</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{YOUR_NAME}' => 'Reporter name',
                    '{YOUR_EMAIL}' => 'Reporter email',
                    '{REPORTED_URL}' => 'Reported URL'
                ]),
                'category' => 'moderation'
            ],

            // Referral Invite (Company)
            [
                'slug' => 'referral-invite-company',
                'name' => 'Referral Invite - Company',
                'subject' => 'You Have Been Invited to Join {SITE_NAME}',
                'body' => '<p>Hello!</p><p><strong>{REFERRER_NAME}</strong> has invited you to join {SITE_NAME}.</p><p>Join our platform to post jobs, find talented candidates, and grow your business.</p><div style="background-color: #f8f9fa; padding: 20px; margin: 20px 0;"><p><strong>Benefits of joining:</strong></p><ul><li>Post jobs and reach thousands of candidates</li><li>Search and filter through qualified resumes</li><li>Manage applications efficiently</li><li>Build your employer brand</li></ul></div><p style="text-align: center;"><a href="{REFERRAL_LINK}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px;">Register Now</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{REFERRER_NAME}' => 'Referrer company name',
                    '{REFERRAL_LINK}' => 'Referral registration link',
                    '{INVITED_EMAIL}' => 'Invited email address'
                ]),
                'category' => 'referral'
            ],

            // Referral Invite (User)
            [
                'slug' => 'referral-invite-user',
                'name' => 'Referral Invite - Job Seeker',
                'subject' => 'You Have Been Invited to Join {SITE_NAME}',
                'body' => '<p>Hello!</p><p><strong>{REFERRER_NAME}</strong> has invited you to join {SITE_NAME}.</p><p>Join our platform to find your dream job and connect with top employers.</p><div style="background-color: #f8f9fa; padding: 20px; margin: 20px 0;"><p><strong>Benefits of joining:</strong></p><ul><li>Access thousands of job opportunities</li><li>Build your professional resume</li><li>Get noticed by top employers</li><li>Receive job alerts matching your skills</li></ul></div><p style="text-align: center;"><a href="{REFERRAL_LINK}" style="display: inline-block; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px;">Register Now</a></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{REFERRER_NAME}' => 'Referrer user name',
                    '{REFERRAL_LINK}' => 'Referral registration link',
                    '{INVITED_EMAIL}' => 'Invited email address'
                ]),
                'category' => 'referral'
            ],

            // Package Receipt
            [
                'slug' => 'package-receipt',
                'name' => 'Package Purchase Receipt',
                'subject' => 'Payment Receipt - {PACKAGE_TITLE}',
                'body' => '<p>Hi {COMPANY_NAME},</p><p>This email confirms your package purchase.</p><table style="width: 100%; border-collapse: collapse; margin: 16px 0; border: 1px solid #e2e8f0;"><tr style="background: #f8fafc;"><th style="text-align: left; padding: 10px;">Package</th><td style="padding: 10px;">{PACKAGE_TITLE}</td></tr><tr><th style="text-align: left; padding: 10px;">Regular Price</th><td style="padding: 10px;">{CURRENCY_CODE} {LIST_PRICE}</td></tr>{DISCOUNT_ROW}<tr style="background: #f8fafc;"><th style="text-align: left; padding: 10px;">Total Paid</th><td style="padding: 10px;"><strong style="color: #17D27C; font-size: 18px;">{CURRENCY_CODE} {AMOUNT_PAID}</strong></td></tr><tr><th style="text-align: left; padding: 10px;">Reference</th><td style="padding: 10px;">{TRANSACTION_ID}</td></tr></table><p>If you have questions, reply to this email or contact support.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{COMPANY_NAME}' => 'Company name',
                    '{PACKAGE_TITLE}' => 'Package title',
                    '{AMOUNT_PAID}' => 'Amount paid',
                    '{LIST_PRICE}' => 'Regular price',
                    '{DISCOUNT_AMOUNT}' => 'Discount amount',
                    '{DISCOUNT_ROW}' => 'Discount row (auto-generated)',
                    '{CURRENCY_CODE}' => 'Currency code',
                    '{TRANSACTION_ID}' => 'Transaction reference'
                ]),
                'category' => 'payment'
            ],

            // Password Reset
            // Password Reset (Web - Link based)
            [
                'slug' => 'password-reset',
                'name' => 'Password Reset Link',
                'subject' => 'Reset Password - {SITE_NAME}',
                'body' => '<p>Hello <strong>{NAME}</strong>,</p><p>You requested to reset your password for your {SITE_NAME} account. Click the button below to reset your password:</p><div style="text-align: center; margin: 30px 0;"><a href="{RESET_LINK}" style="display: inline-block; background: #17D27C; color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px; font-size: 16px;">Reset Password</a></div><p>Or copy and paste this URL in your browser:</p><p style="word-break: break-all; color: #666; font-size: 14px;">{RESET_LINK}</p><p><strong>Important:</strong> This password reset link will expire in 60 minutes. If you didn\'t request this password reset, please ignore this email.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{NAME}' => 'User name',
                    '{RESET_LINK}' => 'Password reset link'
                ]),
                'category' => 'authentication'
            ],

            // Password Reset Code (Mobile App)
            [
                'slug' => 'password-reset-code',
                'name' => 'Password Reset Code',
                'subject' => 'Password Reset Code - {SITE_NAME}',
                'body' => '<p>Hello <strong>{NAME}</strong>,</p><p>You requested to reset your password for your {SITE_NAME} account. Use the verification code below to reset your password:</p><div style="background-color: #F0FDF4; border: 2px solid #17D27C; padding: 20px; text-align: center; margin: 30px 0;"><p style="margin: 0 0 10px 0;">Your verification code is:</p><div style="font-size: 36px; font-weight: bold; color: #17D27C; letter-spacing: 8px;">{CODE}</div><p style="margin: 10px 0 0 0; font-size: 14px;">Enter this code in the app to reset your password</p></div><p><strong>Important:</strong> This code will expire at <strong>{EXPIRES_AT}</strong>. Please use it within 30 minutes.</p><p>If you didn\'t request this password reset, please ignore this email.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{NAME}' => 'User name',
                    '{CODE}' => 'Verification code',
                    '{EXPIRES_AT}' => 'Expiration date/time'
                ]),
                'category' => 'authentication'
            ],

            // Email Verification
            [
                'slug' => 'email-verification',
                'name' => 'Email Verification Code',
                'subject' => 'Email Verification - {SITE_NAME}',
                'body' => '<p>Hello {NAME},</p><p>Thank you for registering with {SITE_NAME}! To complete your registration, please use the verification code below:</p><div style="background: #fff; border: 2px solid #5E2DFA; padding: 20px; text-align: center; margin: 20px 0;"><p><strong>Your verification code is:</strong></p><div style="font-size: 32px; font-weight: bold; color: #5E2DFA; letter-spacing: 5px;">{VERIFICATION_CODE}</div><p><small>This code will expire on {EXPIRES_AT}</small></p></div><p>Enter this code in the mobile app to verify your email address and complete your registration.</p><p><strong>Important:</strong> This code is valid for 30 minutes only.</p><p>If you didn\'t create an account with {SITE_NAME}, please ignore this email.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{NAME}' => 'User name',
                    '{VERIFICATION_CODE}' => 'Verification code',
                    '{EXPIRES_AT}' => 'Expiration date/time'
                ]),
                'category' => 'authentication'
            ],

            // Generic Message
            [
                'slug' => 'generic-message',
                'name' => 'Generic Message Template',
                'subject' => '{SUBJECT}',
                'body' => '<p>Dear {TO_NAME},</p><p>{SUBJECT}</p><p>{MESSAGE}</p><p>You can respond to this email by replying to this email.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{TO_NAME}' => 'Recipient name',
                    '{SUBJECT}' => 'Message subject',
                    '{MESSAGE}' => 'Message content'
                ]),
                'category' => 'general'
            ],

            // Job Recommendation for Job Seeker
            [
                'slug' => 'job-recommendation-jobseeker',
                'name' => 'Job Recommendation - Job Seeker',
                'subject' => 'New Job Opportunity: {JOB_TITLE}',
                'body' => '<p>Hello <strong>{USER_NAME}</strong>,</p><p>We found a new job opportunity that matches your profile on {SITE_NAME}!</p><div style="background-color: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0;"><p><strong>Job Details:</strong></p><ul><li><strong>Position:</strong> {JOB_TITLE}</li><li><strong>Company:</strong> {COMPANY_NAME}</li><li><strong>Location:</strong> {JOB_LOCATION}</li><li><strong>Job Type:</strong> {JOB_TYPE}</li><li><strong>Salary:</strong> {SALARY_RANGE}</li></ul></div><p style="text-align: center;"><a href="{JOB_LINK}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px;">View Job Details</a></p><p><small>This job matches your profile based on your functional area and career level. If you\'re not interested in receiving these recommendations, you can update your job alert preferences in your account settings.</small></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{USER_NAME}' => 'Job seeker name',
                    '{JOB_TITLE}' => 'Job title',
                    '{COMPANY_NAME}' => 'Company name',
                    '{JOB_LOCATION}' => 'Job location',
                    '{JOB_TYPE}' => 'Job type (Full-time, Part-time, etc.)',
                    '{SALARY_RANGE}' => 'Salary range',
                    '{JOB_LINK}' => 'Job detail page link'
                ]),
                'category' => 'recommendation'
            ],

            // Candidate Recommendation for Employer
            [
                'slug' => 'candidate-recommendation-employer',
                'name' => 'Candidate Recommendation - Employer',
                'subject' => 'New Candidate Match: {USER_NAME} for {JOB_TITLE}',
                'body' => '<p>Hello <strong>{COMPANY_NAME}</strong>,</p><p>Great news! A new candidate has registered on {SITE_NAME} whose profile matches your job posting.</p><div style="background-color: #f8f9fa; border-left: 4px solid #11998e; padding: 20px; margin: 20px 0;"><p><strong>Candidate Profile:</strong></p><ul><li><strong>Name:</strong> {USER_NAME}</li><li><strong>Functional Area:</strong> {FUNCTIONAL_AREA}</li><li><strong>Career Level:</strong> {CAREER_LEVEL}</li><li><strong>Location:</strong> {USER_LOCATION}</li></ul><p><strong>Matching Job:</strong></p><ul><li><strong>Position:</strong> {JOB_TITLE}</li><li><strong>Posted:</strong> {JOB_POSTED_DATE}</li></ul></div><p style="text-align: center;"><a href="{USER_PROFILE_LINK}" style="display: inline-block; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px;">View Candidate Profile</a></p><p><small>Note: Contact details are only available after the candidate applies to your job or you unlock their profile. This recommendation is based on matching functional area and career level.</small></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{COMPANY_NAME}' => 'Company name',
                    '{USER_NAME}' => 'Candidate name',
                    '{FUNCTIONAL_AREA}' => 'Candidate functional area',
                    '{CAREER_LEVEL}' => 'Candidate career level',
                    '{USER_LOCATION}' => 'Candidate location',
                    '{JOB_TITLE}' => 'Job title',
                    '{JOB_POSTED_DATE}' => 'Job posted date',
                    '{USER_PROFILE_LINK}' => 'Candidate profile link'
                ]),
                'category' => 'recommendation'
            ],

            // Applicant Contact Message
            [
                'slug' => 'applicant-contact',
                'name' => 'Applicant Contact Message',
                'subject' => 'Contact from: {FROM_NAME}',
                'body' => '<p>Dear {TO_NAME},</p><p>{SUBJECT}</p><p>{MESSAGE}</p><p><span style="color: #fff;text-decoration: none;background: #f25a55; padding: 7px 10px;text-align: center;display: inline-block;margin-top: 20px;">You can respond to this email by replying to this email.</span></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{TO_NAME}' => 'Recipient name',
                    '{FROM_NAME}' => 'Sender name',
                    '{SUBJECT}' => 'Message subject',
                    '{MESSAGE}' => 'Message content'
                ]),
                'category' => 'messaging'
            ],

            // Company Contact Message
            [
                'slug' => 'company-contact',
                'name' => 'Company Contact Message',
                'subject' => 'Enquiry about: {COMPANY_NAME}',
                'body' => '<p>Dear {TO_NAME},</p><p>{SUBJECT}</p><p>{MESSAGE}</p><p><span style="color: #fff;text-decoration: none;background: #f25a55; padding: 7px 10px;text-align: center;display: inline-block;margin-top: 20px;">You can respond to this email by replying to this email.</span></p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{TO_NAME}' => 'Recipient name',
                    '{FROM_NAME}' => 'Sender name',
                    '{COMPANY_NAME}' => 'Company name',
                    '{SUBJECT}' => 'Message subject',
                    '{MESSAGE}' => 'Message content'
                ]),
                'category' => 'messaging'
            ],

            // Document Upload - Account Approved
            [
                'slug' => 'document-account-approved',
                'name' => 'Document Upload - Account Approved',
                'subject' => 'Account Approved - Start Posting Jobs',
                'body' => '<p>Dear {FULL_NAME},</p><p>Congratulations! Your account on {SITE_NAME} has been approved by our Super Administrator. You are now ready to begin posting job listings and connecting with potential candidates.</p><h3>Company/Employer Details:</h3><p><strong>Company Public link:</strong> <a href="{COMPANY_LINK}">{COMPANY_LINK}</a></p><h3>Getting Started:</h3><ol><li>Login to your {SITE_NAME} account.</li><li>Navigate to the Jobs section.</li><li>Click on "Post a New Job" to create your job listing.</li></ol><p>If you have any questions or need assistance, feel free to reach out to our support team with your {SITE_NAME} public profile url.</p><p>Thank you for choosing {SITE_NAME} for your recruitment needs!</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{FULL_NAME}' => 'Company contact name',
                    '{COMPANY_NAME}' => 'Company name',
                    '{COMPANY_LINK}' => 'Company public profile link',
                    '{COMPANY_ADMIN_LINK}' => 'Company admin link'
                ]),
                'category' => 'company'
            ],

            // Document Upload - Resubmit Request
            [
                'slug' => 'document-resubmit-request',
                'name' => 'Document Upload - Resubmit Request',
                'subject' => 'Resubmit Job Posting Request Declined',
                'body' => '<p>Dear {FULL_NAME},</p><p>Thank you for your interest in posting jobs on {SITE_NAME}. Unfortunately, your recent application was declined due to insufficient documentation to verify your business status.</p><h3>Company/Employer Details:</h3><p><strong>Company Public link:</strong> <a href="{COMPANY_LINK}">{COMPANY_LINK}</a></p><h3>Next Steps:</h3><ol><li><strong>Review Your Documents:</strong> Please ensure that you provide clear and valid documents that establish your company\'s legitimacy.</li><li><strong>Resubmit Your Application:</strong> Once you have the necessary documents, login to your {SITE_NAME} account and resubmit your application.</li><li><strong>Prompt Review:</strong> Our team will expedite the review process upon receiving your updated application.</li></ol><p>We appreciate your commitment to {SITE_NAME} and apologize for any inconvenience. If you have any questions or need assistance, feel free to reach out to our support team.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{FULL_NAME}' => 'Company contact name',
                    '{COMPANY_NAME}' => 'Company name',
                    '{COMPANY_LINK}' => 'Company public profile link'
                ]),
                'category' => 'company'
            ],

            // Document Upload - Pending Verification (To Company)
            [
                'slug' => 'document-pending-company',
                'name' => 'Document Upload - Pending Verification (Company)',
                'subject' => 'Registration Verification in Progress',
                'body' => '<p>Dear {FULL_NAME},</p><p>Thank you for submitting your application to register as an employer on {SITE_NAME}. We have received your request and attached documents.</p><h3>Company/Employer Details:</h3><p><strong>Company Public link:</strong> <a href="{COMPANY_LINK}">{COMPANY_LINK}</a></p><p>Our review team is currently assessing your submission. Please allow some time for the verification process. You will receive a notification email once your account has been verified.</p><p>If you have any questions or need further assistance, feel free to reach out to us.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{FULL_NAME}' => 'Company contact name',
                    '{COMPANY_NAME}' => 'Company name',
                    '{COMPANY_LINK}' => 'Company public profile link'
                ]),
                'category' => 'company'
            ],

            // Document Upload - New Registration (To Admin)
            [
                'slug' => 'document-pending-admin',
                'name' => 'Document Upload - New Registration (Admin)',
                'subject' => 'New Employer Registration Approval Required',
                'body' => '<p>Dear Super Admin,</p><p>We hope this message finds you well. We would like to bring to your attention that a new company or employer has recently registered on {SITE_NAME}. Before they can begin posting job listings, we kindly request your approval.</p><h3>Company/Employer Details:</h3><p><strong>Company Public link:</strong> <a href="{COMPANY_LINK}">{COMPANY_LINK}</a></p><p><strong>Administrator\'s backend link:</strong> <a href="{COMPANY_ADMIN_LINK}">{COMPANY_ADMIN_LINK}</a></p><p>Please login to your Super Administrator account on {SITE_NAME} and review the registration details. If everything appears satisfactory, kindly approve their account to enable them to start posting jobs.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{FULL_NAME}' => 'Company contact name',
                    '{COMPANY_NAME}' => 'Company name',
                    '{COMPANY_LINK}' => 'Company public profile link',
                    '{COMPANY_ADMIN_LINK}' => 'Company admin backend link'
                ]),
                'category' => 'company'
            ],

            // Job Seeker Rejected
            [
                'slug' => 'job-seeker-rejected',
                'name' => 'Job Seeker Application Rejected',
                'subject' => 'Application Status Update - {JOB_TITLE}',
                'body' => '<p>Dear {USER_NAME},</p><p>Thank you for your interest in the <strong>{JOB_TITLE}</strong> position at <strong>{COMPANY_NAME}</strong>.</p><p>After careful consideration, we regret to inform you that your application was not selected for this position. We appreciate your interest and encourage you to apply for other opportunities on {SITE_NAME}.</p><p><strong>Job Link:</strong> <a href="{JOB_LINK}">{JOB_LINK}</a></p><p><strong>Company Profile:</strong> <a href="{COMPANY_LINK}">{COMPANY_LINK}</a></p><p>We wish you the best in your job search.</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{USER_NAME}' => 'Job seeker name',
                    '{COMPANY_NAME}' => 'Company name',
                    '{COMPANY_LINK}' => 'Company profile link',
                    '{JOB_TITLE}' => 'Job title',
                    '{JOB_LINK}' => 'Job detail page link'
                ]),
                'category' => 'application'
            ],

            // Web Email Verification (with link)
            [
                'slug' => 'web-email-verification',
                'name' => 'Web Email Verification (Link)',
                'subject' => 'Email Verification - {SITE_NAME}',
                'body' => '<p>Dear {TO_NAME},</p><p>Thank you for registering with {SITE_NAME}! We\'re excited to have you on board. To get started, please verify your email address by clicking the button below.</p><div style="text-align: center; margin: 30px 0;"><a href="{VERIFICATION_LINK}" style="display: inline-block; background: #5E2DFA; color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px; font-size: 16px;">Click here to verify your account</a></div><p>Or copy and paste this URL in your browser:</p><p style="word-break: break-all; color: #666; font-size: 14px;">{VERIFICATION_LINK}</p><p>Warm regards,<br>{SITE_NAME} Team</p>',
                'shortcodes' => json_encode([
                    '{SITE_NAME}' => 'Website name',
                    '{SITE_URL}' => 'Website URL',
                    '{TO_NAME}' => 'User name',
                    '{VERIFICATION_LINK}' => 'Email verification link'
                ]),
                'category' => 'authentication'
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
