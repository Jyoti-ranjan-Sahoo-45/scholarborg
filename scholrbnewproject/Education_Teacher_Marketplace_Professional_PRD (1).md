# Product Requirements Document (PRD)

## Education Teacher Marketplace & Learning Platform

**Document Type:** Product Requirements Document  
**Version:** 2.0  
**Status:** Product Definition / Development Baseline  
**Primary Model:** Teacher Marketplace + Booking + Learning + Payments + CRM  
**Core Differentiation:** Teachers do not purchase leads; the platform owns the transaction and learning journey.

---

## 1. Executive Summary

The platform is a configurable education marketplace and learning platform connecting students and parents with verified teachers.

Students and parents can discover, compare and match with teachers, book demonstrations, select teachers, purchase packages, schedule classes, attend live lessons, access learning materials, communicate with teachers, and monitor learning activity.

Teachers receive students through the marketplace rather than purchasing leads. Teachers manage their profiles, verification, availability, demos, classes, learning materials, students, earnings and payouts.

The platform also provides a sales CRM that tracks the user journey from anonymous website activity through signup, demo, payment, active learning and retention.

The platform must be **configuration-driven**: administrators control configurable business policies, marketplace rules, financial rules, CRM rules, notifications and operational settings through the Admin Panel without requiring code changes for ordinary business-policy changes.

Security, payment integrity, financial ledger integrity, authentication and other system-level safeguards remain enforced by the platform and cannot be weakened by ordinary business configuration.

---

## 2. Product Vision

Build a trusted, transaction-led education marketplace where the platform controls the complete lifecycle:

**Discovery → Matching → Demo → Teacher Selection → Booking → Payment → Escrow → Learning → Completion → Payout → Retention**

The platform should combine:

1. Teacher marketplace
2. Scheduling and booking engine
3. Live learning platform
4. Parent/student experience
5. Payment and financial engine
6. Teacher payout system
7. Sales CRM
8. Trust and safety
9. Product analytics
10. Admin/configuration platform

---

## 3. Business Objectives

### Primary Objectives

- Build a trusted teacher discovery marketplace.
- Allow students/parents to directly choose teachers.
- Convert teacher discovery into demos and paid classes.
- Manage the complete booking and learning lifecycle.
- Provide teachers with a complete operating dashboard.
- Provide parents with visibility into children's learning.
- Securely manage payments, escrow, refunds and payouts.
- Provide sales teams with actionable user intelligence.
- Maximize student retention and repeat purchases.
- Provide administrators with configuration, reporting and audit controls.

### Core Business Principle

> **The platform does not sell student leads to teachers.**

Teachers receive students through the marketplace and the platform earns through configurable transaction/platform monetization.

---

## 4. Product Scope

### In Scope

- Public education marketplace
- Student portal
- Parent portal
- Teacher portal
- Sales CRM
- Admin portal
- Teacher verification/KYC
- Search and matching
- Availability and booking
- Demo classes
- Packages and recurring classes
- Live classes through Microsoft Teams
- Attendance
- Learning materials
- Communication
- Payments
- Escrow
- Financial ledger
- Refunds/disputes
- Teacher payouts
- Reviews
- Coupons/referrals
- Notifications
- Support
- Fraud and trust controls
- Analytics
- CMS and SEO
- Audit and configuration

### Out of Scope Unless Explicitly Enabled

- Any feature not defined in the approved release scope
- Unapproved payment methods
- Unapproved external communication channels
- Custom business logic implemented outside the configuration/rule system

---

## 5. User Roles & Access Model

### Visitor
Anonymous marketplace visitor with tracked session activity subject to applicable consent/privacy rules.

### Student
Learner who searches, books, purchases, attends and reviews classes.

### Parent
Responsible account holder who manages children, payments and learning visibility where applicable.

### Teacher
Verified service provider who manages profile, availability, demos, classes, students, content and earnings.

### Sales Executive
Manages assigned leads, follow-ups, activity and conversion.

### Sales Manager
Manages sales team, assignment, targets, reports and performance.

### Admin
Manages platform operations and configurable business policies.

### Super Admin
Highest operational role with restricted system-level administration.

### Permission Model

Role-based access control must support:

- Module permissions
- View/create/update/delete permissions
- Financial permissions
- Verification permissions
- Refund permissions
- Payout permissions
- Audit access
- Configuration access
- User-management permissions

Sensitive operations require elevated permissions and should support maker-checker approval where appropriate.

---

## 6. Configuration-Driven Business Logic

This is a core platform requirement.

### 6.1 Principle

Business policies should be stored as configurable rules rather than hard-coded values wherever practical.

```text
ADMIN CONFIGURATION
        ↓
BUSINESS RULE ENGINE
        ↓
MARKETPLACE / BOOKING / PAYMENT / CRM / LEARNING
```

### 6.2 Admin-Configurable Rules

Admin should be able to configure:

#### Marketplace
- Teacher ranking weights
- Matching weights
- Featured teacher rules
- Teacher visibility
- Category rules
- Search/filter options
- Profile publication rules

#### Teacher
- Verification requirements
- Approval workflow
- Required documents
- Profile completion threshold
- Suspension rules
- Re-verification rules
- Pricing boundaries

#### Demo
- Demo availability
- Demo duration
- Demo pricing
- Demo cancellation rules
- Demo reminder timing
- Demo conversion window

#### Booking
- Slot duration
- Buffer time
- Minimum booking notice
- Maximum advance booking
- Slot lock duration
- Rescheduling window
- No-show rules
- Waitlist rules

#### Packages
- Package validity
- Number of classes
- Expiry
- Extension
- Freeze/pause
- Renewal
- Unused-class treatment

#### Payments
- Platform commission
- Student fee
- Teacher fee
- Gateway fee responsibility
- Tax/GST treatment
- Minimum/maximum fees
- Payment methods

#### Escrow & Payout
- Escrow release conditions
- Release timing
- Minimum payout
- Payout frequency
- Teacher payout eligibility
- Holding period

#### Refund & Cancellation
- Cancellation windows
- Refund percentage
- Teacher compensation
- Penalties
- Gateway fee treatment
- Automatic/manual refund
- Dispute rules

#### CRM
- Lead scoring
- Lead stages
- Lead assignment
- Round-robin rules
- Follow-up SLA
- Hot/warm/cold thresholds
- Sales targets

#### Promotions
- Coupons
- Discounts
- Referral rewards
- Affiliate rules
- Campaign eligibility
- Usage limits

#### Communication
- Notification triggers
- Channels
- Reminder timing
- Templates
- Marketing preferences

#### Trust & Safety
- Fraud thresholds
- Anti-bypass rules
- Review rules
- Suspension thresholds
- Risk scoring

### 6.3 Rule Versioning

Every configurable rule must support:

- Rule ID
- Name
- Description
- Value
- Conditions
- Priority
- Status
- Effective date
- Expiry date
- Version
- Created by
- Updated by
- Change history

Historical transactions must retain the rule/version used when the transaction was calculated.

### 6.4 Protected System Controls

Admin configuration must not bypass:

- Authentication security
- Authorization boundaries
- Payment signature verification
- Financial ledger integrity
- Encryption requirements
- System audit integrity
- Mandatory privacy/security safeguards

---

## 7. End-to-End Customer Journey

```text
Visitor
  ↓
Search / Browse
  ↓
Teacher Profile
  ↓
Requirement Capture
  ↓
Matching / Comparison
  ↓
Demo Booking
  ↓
Demo Attendance
  ↓
Teacher Selection
  ↓
Package Selection
  ↓
Payment
  ↓
Booking
  ↓
Calendar / Teams
  ↓
Class
  ↓
Attendance / Learning
  ↓
Completion
  ↓
Escrow Release
  ↓
Teacher Payout
  ↓
Review
  ↓
Retention / Renewal
```

---

## 8. Functional Requirements

The detailed functional requirements from the approved source PRD are retained below and organized as the functional baseline.

# 2. Product Goals

### Primary Goals

1. Build a trusted teacher discovery marketplace.
2. Allow students to directly choose teachers.
3. Allow students to book demo classes.
4. Convert demo students into paid students.
5. Manage complete class scheduling and booking.
6. Provide teachers with a complete teaching dashboard.
7. Provide parents with complete visibility into children's learning.
8. Manage payments, escrow, refunds and teacher payouts.
9. Provide a complete communication system.
10. Give the sales team detailed user behavior and lead information.
11. Give administrators complete control and auditing capabilities.

# 3. User Roles

The platform will have the following major roles:

### 3.1 Visitor

A person who visits the website without registering.

Can:

- Browse teachers
- Search subjects
- Search classes/grades
- View teacher profiles
- Apply filters
- View pricing/packages
- Start registration
- Book/contact actions where permitted

Their activity should be tracked using an anonymous/session identifier.

### 3.2 Student

Can:

- Create account
- Search teachers
- Filter teachers
- Compare teachers
- View teacher profiles
- Book demo classes
- Purchase class packages
- Book classes
- Attend live classes
- View calendar
- Communicate with teachers
- View assignments/materials
- Track attendance
- View payments
- Submit reviews
- Manage profile

### 3.3 Parent

Can:

- Create parent account
- Add/manage children
- View child's teachers
- View classes
- View calendar
- Monitor attendance
- View learning activity
- View payments
- Book/pay for classes where authorized
- Communicate with teachers
- Receive notifications
- View reports

### 3.4 Teacher

Can:

- Register
- Complete teacher profile
- Submit verification documents
- Select subjects
- Select grades/classes
- Set pricing
- Configure availability
- Define demo availability
- Manage students
- Manage demos
- Manage classes
- Conduct one-to-one classes
- Conduct group/live classes
- Use Microsoft Teams for live classes
- Manage calendar
- Communicate with students/parents
- Upload learning materials
- Manage attendance
- View earnings
- View escrow balance
- Request payouts
- Receive reviews

### 3.5 Sales Team

Sales users can:

- View new users
- View leads
- View user interests
- View activity history
- View search history
- View teacher interactions
- View demo activity
- View payment activity
- View lead score
- Assign leads
- Add notes
- Schedule follow-ups
- Update lead status
- Track conversion

### 3.6 Admin

Admin has complete platform control.

Can manage:

- Students
- Parents
- Teachers
- Sales users
- Classes
- Demos
- Payments
- Escrow
- Payouts
- Refunds
- Reviews
- Communication
- Marketplace
- Categories
- Subjects
- Reports
- User activity
- Sales
- Audit logs
- Platform settings

# 4. Public Website

The public website should have a modern education-focused design.

### Main Pages

- Home
- Find Teachers
- Teacher Listing
- Teacher Profile
- Subjects
- Classes/Grades
- How It Works
- Become a Teacher
- Pricing
- About Us
- Contact
- FAQ
- Login
- Registration

# 5. Teacher Marketplace

The marketplace is the core of the platform.

Students should be able to discover teachers based on their requirements.

### Teacher Listing

Each teacher card should display:

- Profile photo
- Name
- Verification badge
- Qualification
- Experience
- Subjects
- Classes/grades
- Rating
- Number of reviews
- Starting price
- Teaching mode
- Demo availability
- Languages
- Short introduction
- Book Demo button
- View Profile button

# 6. Teacher Search & Filters

Students should be able to search by:

- Subject
- Class/grade
- Education board
- Language
- Location
- Price range
- Experience
- Rating
- Availability
- Teaching mode
- One-to-one
- Group classes

### Search

Examples:

- Maths teacher
- Class 10 Maths
- CBSE Science teacher
- English teacher
- Physics teacher

# 7. Teacher Profile

Each teacher should have a detailed profile.

### Profile Sections

- Profile photo
- Introduction
- Qualification
- Experience
- Subjects
- Classes
- Boards
- Teaching languages
- Teaching methodology
- Pricing
- Demo information
- Availability
- Reviews
- Ratings
- Class types
- Number of students/classes
- Verification status

### Actions

- Book Demo
- Select Teacher
- Contact/Message
- View Availability
- View Packages

# 8. Teacher Verification

Teachers should go through an admin verification process.

### Verification information

- Identity information
- Qualification
- Certificates
- Experience
- Profile information
- Bank/payout information

### Status

- Pending
- Under Review
- Verified
- Rejected
- Suspended

Only approved teachers should be visible/bookable according to platform rules.

# 9. Student Journey

The primary student journey:

```text
Visit Website
      ↓
Search Subject/Class
      ↓
View Teachers
      ↓
Compare Teachers
      ↓
View Teacher Profile
      ↓
Book Demo
      ↓
Demo Confirmation
      ↓
Attend Demo
      ↓
Demo Completed
      ↓
Student Selects Teacher
      ↓
Select Package
      ↓
Payment
      ↓
Escrow
      ↓
Classes Scheduled
      ↓
Live Classes
      ↓
Attendance
      ↓
Learning Activity
      ↓
Teacher Payment/Payout
```

# 10. Demo Class System

Students should be able to book a demo with a teacher.

### Demo Workflow

```text
Requested
   ↓
Confirmed
   ↓
Scheduled
   ↓
Reminder
   ↓
Demo Started
   ↓
Demo Completed
   ↓
Feedback
   ↓
Converted / Not Converted
```

### Demo Features

- Teacher availability
- Student availability
- Calendar integration
- Demo booking
- Confirmation
- Reminder
- Teams meeting link
- Attendance
- Feedback
- Conversion tracking

# 11. Class Booking System

After the demo, students can purchase classes.

### Class Types

#### One-to-One

Student ↔ Teacher

#### Group Class

Multiple Students ↔ Teacher

### Class Packages

Examples:

- 4 classes
- 8 classes
- 12 classes
- Monthly package
- Custom package

The system should track:

- Purchased classes
- Used classes
- Remaining classes
- Scheduled classes
- Completed classes
- Cancelled classes

# 12. Recurring Classes

Teachers/students should be able to create recurring schedules.

Example:

**Monday, Wednesday & Friday — 6:00 PM**

The system automatically generates the applicable class schedule.

# 13. Calendar System

Every major role should have a calendar.

### Student Calendar

- Demo classes
- Upcoming classes
- Completed classes
- Cancelled classes

### Parent Calendar

- Child's classes
- Demos
- Important learning events

### Teacher Calendar

- Demo classes
- One-to-one classes
- Group classes
- Availability
- Holidays

### Admin Calendar

- Platform-wide classes
- Demos
- Important operational events

# 14. Microsoft Teams Integration

Microsoft Teams will be used for live classes.

### Workflow

```text
Class Scheduled
      ↓
Teams Meeting Created/Linked
      ↓
Meeting Link Stored
      ↓
Calendar Updated
      ↓
Notifications Sent
      ↓
Teacher & Student Join
      ↓
Class Completed
      ↓
Attendance/Class Status Updated
```

The platform should maintain the relationship between:

**Platform Class ID → Teams Meeting → Student → Teacher**

# 15. Attendance

For every class:

- Present
- Absent
- Late
- Cancelled
- Rescheduled

Attendance should be visible to:

- Student
- Parent
- Teacher
- Admin

# 16. Learning Management

Teachers should be able to manage learning content.

### Features

- Study materials
- PDFs
- Documents
- Assignments
- Homework
- Student submissions
- Teacher feedback
- Class notes
- Learning resources

Students can access relevant materials from their dashboard.

# 17. Parent Portal

The parent portal is an important part of the platform.

### Dashboard

Parent should see:

- Children
- Active teachers
- Upcoming classes
- Calendar
- Attendance
- Payments
- Class history
- Learning activity
- Notifications
- Messages

### Child Management

Parent can:

- Add child
- View child profile
- View teachers
- View classes
- View attendance
- View payments
- Monitor learning activity

# 18. Communication System

The platform should include internal communication.

### Communication Channels

- Student ↔ Teacher
- Parent ↔ Teacher
- Student ↔ Admin
- Parent ↔ Admin
- Teacher ↔ Admin
- Sales ↔ Users

### Features

- One-to-one chat
- File sharing
- Notifications
- Read/unread status
- Message history
- System announcements

# 19. Payment System

The platform will manage payments between students and teachers.

### Payment Flow

```text
Student
   ↓
Payment Gateway
   ↓
Platform
   ↓
Escrow/Holding Logic
   ↓
Class/Service Completion
   ↓
Eligible Teacher Amount
   ↓
Platform Commission
   ↓
Teacher Payout
```

# 20. Escrow System

The system should maintain separate financial states.

### Teacher Balance

- Escrow Balance
- Pending Balance
- Available Balance
- Withdrawable Balance
- Paid Balance

### Transaction Types

- Payment
- Escrow Hold
- Escrow Release
- Commission
- Refund
- Partial Refund
- Adjustment
- Withdrawal
- Payout
- Failed Payment
- Cancellation

Every financial transaction should have a unique transaction reference.

# 21. Cancellation & Refund

The platform should define cancellation policies.

### Scenarios

- Student cancellation
- Teacher cancellation
- Late cancellation
- Teacher no-show
- Student no-show
- Class rescheduling
- Technical issue
- Dispute

The system should calculate the applicable:

- Refund
- Partial refund
- Escrow release
- Teacher compensation
- Platform commission adjustment

# 22. Dispute Management

Students, parents and teachers should be able to raise disputes.

### Example

Student reports:

> Teacher did not conduct the class.

Teacher reports:

> Student did not attend.

Admin can review:

- Booking
- Payment
- Class status
- Attendance
- Teams meeting information
- Messages
- Cancellation history
- Previous activity

Admin can decide:

- Release payment
- Refund
- Partial refund
- Reschedule
- Reject dispute

# 23. Teacher Earnings & Payouts

Teacher dashboard should display:

- Total earnings
- Pending earnings
- Escrow amount
- Available balance
- Withdrawable amount
- Paid amount
- Commission
- Transaction history

### Withdrawal

```text
Teacher requests withdrawal
        ↓
Validation
        ↓
Admin/Payment Processing
        ↓
Payout
        ↓
Transaction Updated
```

# 24. Reviews & Ratings

Students/parents can review eligible teachers after completed classes/demos.

### Rating

- Overall rating
- Teaching quality
- Communication
- Punctuality
- Review

Only eligible users should be allowed to submit reviews.

# 25. Sales CRM

The sales system is a core module.

The platform should not only store registered users.

It should track users from their **first website interaction**.

### User Lifecycle

```text
Anonymous Visitor
      ↓
Tracked Visitor
      ↓
Signup Started
      ↓
Registered
      ↓
Lead
      ↓
Demo
      ↓
Customer
      ↓
Active Student
```

# 26. Pre-Signup Tracking

Before a user creates an account, the system should track activity using an anonymous/session identifier.

### Track

- Anonymous user ID
- Session ID
- Device
- Browser
- Operating system
- Approximate location
- Landing page
- Pages visited
- Search queries
- Subjects searched
- Classes searched
- Teachers viewed
- Filters used
- Price ranges viewed
- Demo interactions
- Signup started
- Signup abandoned
- Referral source
- Campaign
- UTM parameters

Tracking must follow applicable privacy, consent and data-protection requirements.

# 27. Post-Signup User Tracking

After registration, anonymous activity should be associated with the user's account where permitted.

### User 360 Profile

```text
Basic Information
       +
Acquisition Information
       +
Interests
       +
Search Activity
       +
Teacher Activity
       +
Demo Activity
       +
Class Activity
       +
Payment Activity
       +
Sales Activity
```

# 28. User Interests

The system should identify user interests.

### Examples

- Subject
- Class
- Board
- Preferred teacher type
- Location
- Language
- Price range
- One-to-one
- Group class

Example:

> User interested in Class 10 CBSE Mathematics and Science, prefers one-to-one classes, and viewed teachers between ₹3,000–₹5,000.

# 29. Activity Timeline

Every important activity should be stored.

Example:

```text
10:02 AM  Homepage visited
10:04 AM  Searched Class 10 Maths
10:06 AM  CBSE filter applied
10:08 AM  Teacher profile viewed
10:12 AM  Pricing viewed
10:15 AM  Signup started
10:17 AM  Account created
10:21 AM  Demo booked
10:25 AM  Salesperson assigned
10:40 AM  Sales follow-up completed
11:10 AM  Payment completed
```

# 30. Lead Management

Sales team should be able to manage leads.

### Lead Status

- New
- Contacted
- Follow-up
- Demo Booked
- Demo Completed
- Interested
- Payment Pending
- Converted
- Not Interested
- Lost
- Closed

# 31. Lead Assignment

Admin/sales manager can assign leads to salespeople.

Each lead should show:

- User
- Role
- Location
- Interests
- Source
- Recent activity
- Lead score
- Demo status
- Payment status
- Assigned salesperson
- Follow-up date
- Notes
- Conversion status

# 32. Lead Scoring

The system should calculate user intent based on activity.

Example scoring:

```text
Search subject                 +5
View teacher                   +10
View multiple teachers         +15
View pricing                   +10
Start signup                   +15
Book demo                      +20
Attend demo                    +25
Start payment                  +30
Complete payment               +50
```

The scoring values should be configurable by admin.

### Lead Categories

- Cold
- Warm
- Hot
- Converted

# 33. Sales Follow-Up

Salespeople should be able to:

- Add notes
- Schedule follow-ups
- Update lead status
- Record call outcome
- Record conversation notes
- Assign/reassign lead
- View complete activity
- Track conversion

# 34. Admin Dashboard

Admin dashboard should provide a complete overview.

### User Metrics

- Total users
- New students
- New parents
- New teachers
- Active users
- Verified teachers

### Marketplace

- Teacher profiles
- Demo bookings
- Teacher conversion
- Most searched subjects
- Most viewed teachers

### Classes

- Upcoming
- Completed
- Cancelled
- Rescheduled
- Attendance

### Financial

- Total transactions
- Platform revenue
- Teacher earnings
- Escrow
- Refunds
- Payouts

### Sales

- New leads
- Hot leads
- Follow-ups
- Demo conversion
- Payment conversion

# 35. Admin Management

Admin should manage:

### Users

- Students
- Parents
- Teachers
- Salespeople
- Admins

### Marketplace

- Subjects
- Classes
- Boards
- Categories
- Filters
- Teacher visibility

### Classes

- All classes
- Demos
- Active classes
- Completed classes
- Cancelled classes

### Finance

- Payments
- Escrow
- Refunds
- Commissions
- Payouts

### Communication

- Messages
- Reports
- Notifications
- Announcements

# 36. Audit System

Admin should be able to audit important platform activity.

Every important action should record:

- User
- Role
- Action
- Entity
- Previous value
- New value
- Timestamp
- IP/device information where appropriate

### Examples

```text
Teacher changed price
Admin approved teacher
Student cancelled class
Admin issued refund
Teacher requested payout
Admin released escrow
Salesperson changed lead status
Admin changed user role
```

Audit records should be tamper-resistant and accessible only to authorized users.

# 37. Notifications

### Student

- Signup
- Demo confirmation
- Demo reminder
- Class reminder
- Payment
- Teacher message
- Class cancellation
- Assignment

### Parent

- Child class reminder
- Attendance
- Payment
- Teacher message
- Learning updates

### Teacher

- New demo
- New student
- New booking
- Payment
- Class reminder
- Cancellation
- Payout

### Sales

- New lead
- High-intent lead
- Demo booked
- Payment abandoned
- Follow-up due

# 38. Reporting

Admin reports should include:

### User Reports

- Registration
- Active users
- User growth
- Role distribution

### Marketplace Reports

- Searches
- Teacher views
- Demo bookings
- Teacher conversions

### Sales Reports

- Leads
- Lead sources
- Salesperson performance
- Follow-ups
- Conversion rate

### Financial Reports

- GMV
- Platform commission
- Teacher earnings
- Refunds
- Escrow
- Payouts

### Learning Reports

- Classes conducted
- Attendance
- Cancellations
- Rescheduling

# 39. Security & Access Control

The platform should use role-based access control.

### Roles

- Super Admin
- Admin
- Sales Manager
- Sales Executive
- Teacher
- Parent
- Student

Each role should have configurable permissions.

Sensitive areas such as:

- Payments
- Payouts
- User data
- Audit logs
- Teacher verification
- Refunds

must have restricted access.

# 40. Core Business Rules

### Teacher

Teachers do not purchase student leads.

### Student

Students choose and book teachers directly.

### Demo

Demo booking should be linked to the selected teacher and student's calendar.

### Payment

Paid bookings must create corresponding financial records.

### Escrow

Teacher funds should follow the platform's defined escrow/release rules.

### Class

Every booked class must have:

- Student
- Teacher
- Date
- Time
- Class type
- Status

### Calendar

Every confirmed class should appear in the appropriate calendars.

### Audit

Critical administrative and financial actions must be logged.

# 41. Core Status Systems

### User

`Active → Inactive → Suspended → Blocked`

### Teacher

`Pending → Under Review → Verified → Rejected → Suspended`

### Demo

`Requested → Confirmed → Scheduled → Completed → Converted / Not Converted → Cancelled`

### Class

`Scheduled → Live → Completed`

or

`Scheduled → Cancelled / Rescheduled`

### Payment

`Pending → Paid → Escrowed → Released`

or

`Pending → Failed / Refunded`

### Lead

`New → Contacted → Follow-up → Demo → Interested → Converted / Lost`

# 42. Overall Platform Architecture

```text
                         PLATFORM
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
   MARKETPLACE          LEARNING             CRM
        │                   │                   │
   Teachers              Classes             Leads
   Search                Demos               Activity
   Profiles              Teams               Interests
   Reviews               Calendar             Sales
   Booking               Attendance           Follow-ups
        │                   │                   │
        └───────────────────┼───────────────────┘
                            │
                    COMMUNICATION
                            │
                 Student ↔ Teacher
                 Parent ↔ Teacher
                 Admin ↔ Users
                            │
                            │
                     PAYMENT ENGINE
                            │
                Payment → Escrow → Payout
                            │
                            │
                       ADMIN PANEL
                            │
              ┌─────────────┼─────────────┐
              │             │             │
           Reports        Audit         Settings
```

# 43. MVP Priority

### Phase 1 — Core Marketplace

- User registration/login
- Student
- Parent
- Teacher
- Teacher verification
- Teacher profiles
- Search
- Filters
- Teacher listing
- Teacher profile
- Demo booking
- Calendar

### Phase 2 — Learning

- Class booking
- Packages
- One-to-one classes
- Group classes
- Microsoft Teams integration
- Attendance
- Learning materials
- Communication

### Phase 3 — Financial

- Payment gateway
- Escrow logic
- Commission
- Refund
- Teacher balance
- Payout
- Financial reports

### Phase 4 — CRM & Sales

- Visitor tracking
- Signup tracking
- User activity
- Search history
- Interest tracking
- Lead management
- Lead scoring
- Sales assignment
- Follow-up
- Sales reports

### Phase 5 — Admin & Audit

- Complete admin dashboard
- Role permissions
- Audit logs
- Dispute management
- Advanced reports
- Platform configuration

# 44. Final Product Definition

The final platform should be positioned as:

> **A complete digital education marketplace where students and parents discover and select verified teachers, book demos, purchase classes, attend live lessons, communicate with teachers, and manage their learning journey — while teachers manage students, classes, schedules and earnings, and the platform manages payments, escrow, sales, operations and complete administrative auditing.**

The key difference from an UrbanPro-style lead marketplace is:

**You don't sell leads.**

Instead:

**You own the transaction and learning journey.**

```text
DISCOVERY
    ↓
TEACHER MARKETPLACE
    ↓
DEMO
    ↓
TEACHER SELECTION
    ↓
PACKAGE / CLASS BOOKING
    ↓
PAYMENT
    ↓
ESCROW
    ↓
CALENDAR
    ↓
MICROSOFT TEAMS CLASS
    ↓
ATTENDANCE / LEARNING
    ↓
COMPLETION
    ↓
TEACHER PAYOUT
    ↓
PARENT / STUDENT MONITORING
    ↓
ADMIN AUDIT + SALES ANALYTICS
```

# 45. Business Model & Monetization

The platform owns the complete transaction and learning journey. Revenue should be configurable by administrators.

### Revenue Sources

- Platform commission on teacher earnings
- Student service/convenience fee where applicable
- Teacher subscription plans where applicable
- Featured/sponsored teacher placement
- Promotional campaigns
- Referral/affiliate revenue
- Cancellation fees where applicable
- Other configurable platform fees

### Commission Rules

Admin should be able to configure:

- Global commission percentage
- Teacher-specific commission
- Subject/category-specific commission
- Package-specific commission
- Promotional commission
- Minimum platform fee
- Maximum platform fee
- Gateway fee responsibility
- Tax/GST treatment

### Example

```text
Student Payment
      ↓
Payment Gateway Fee
      ↓
Applicable Tax
      ↓
Platform Commission
      ↓
Teacher Earning
      ↓
Escrow
      ↓
Eligible Payout
```

All calculations must be stored against the financial transaction and must be auditable.

# 46. Student Onboarding & Requirement Profile

Student onboarding should capture the information required to provide relevant teacher recommendations.

### Student Requirements

- Student name
- Age
- Class/grade
- Education board
- Subjects
- Current learning level
- Learning goals
- Exam target
- Preferred language
- Preferred teaching mode
- Preferred class timings
- Budget
- Location/timezone
- One-to-one/group preference

The system should create a Student Requirement Profile that can be used by search, matching and sales teams.

# 47. Teacher Onboarding & KYC

Teacher onboarding should be treated as a structured workflow.

```text
Application
↓
Mobile/Email Verification
↓
Profile Creation
↓
Qualification Upload
↓
Experience Verification
↓
Identity/KYC Verification
↓
Admin Review
↓
Interview/Assessment where applicable
↓
Approved
↓
Profile Published
```

### Teacher Onboarding Data

- Identity information
- Profile photo
- Qualification
- Certificates
- Experience
- Subjects
- Classes
- Boards
- Languages
- Teaching methodology
- Introduction/video introduction where applicable
- Bank details
- PAN
- GST/tax information where applicable
- Verification documents

### Teacher Profile Completion

The system should calculate profile completion percentage and identify missing information before publication.

### Verification Controls

- Verification expiry
- Re-verification
- Rejection reason
- Admin comments
- Suspension reason
- Document review history

# 48. Availability & Scheduling Engine

Availability is a core marketplace capability.

### Teacher Availability

Teachers should be able to configure:

- Weekly recurring availability
- Specific-date availability
- Blocked dates
- Holidays
- Break periods
- Buffer time
- Minimum booking notice
- Maximum advance booking
- Slot duration
- Timezone
- Emergency unavailability

### Scheduling Rules

The system must:

- Prevent double booking
- Detect conflicts
- Temporarily lock selected slots during checkout
- Release expired slot locks
- Handle recurring schedules
- Handle teacher/student rescheduling
- Respect teacher holidays and blocked periods
- Respect student availability

### Slot Example

```text
Teacher availability: 5 PM–9 PM

Booked: 6 PM–7 PM

Available:
5 PM–6 PM
7 PM–8 PM
8 PM–9 PM
```

# 49. Booking Engine

The booking engine controls demos, classes and recurring bookings.

### Booking Lifecycle

```text
Slot Selected
↓
Slot Temporarily Locked
↓
Payment/Confirmation
↓
Booking Confirmed
↓
Calendar Updated
↓
Notifications Sent
↓
Class/Demo Conducted
↓
Completed / Cancelled / Rescheduled
```

### Booking Requirements

Every booking must have:

- Unique booking ID
- Student
- Parent where applicable
- Teacher
- Date
- Start time
- End time
- Timezone
- Class type
- Package reference where applicable
- Payment reference where applicable
- Status
- Creation timestamp

### Booking Controls

- Duplicate booking prevention
- Payment timeout
- Slot expiration
- Rescheduling
- Cancellation
- No-show
- Waiting list
- Grace period
- Late joining
- Conflict detection

# 50. Package Lifecycle Management

Every class package must have a complete lifecycle.

### Package Information

- Package ID
- Package name
- Number of classes
- Price
- Start date
- Expiry date
- Classes purchased
- Classes used
- Classes remaining
- Package status

### Package Operations

- Purchase
- Activate
- Pause
- Resume
- Renew
- Extend
- Cancel
- Refund
- Expire

### Business Rules

The system must define:

- Package expiry
- Unused class policy
- Extension policy
- Freeze period
- Cancellation policy
- Refund for unused classes
- Teacher cancellation treatment
- Student cancellation treatment
- No-show deduction
- Rescheduled class treatment

# 51. Payment Gateway & Payment Processing

The platform must maintain a reliable payment lifecycle.

```text
Payment Initiated
↓
Payment Order Created
↓
Gateway Processing
↓
Gateway Success/Failure
↓
Webhook Received
↓
Payment Verified
↓
Booking Confirmed
↓
Financial Ledger Updated
```

### Payment Requirements

- Payment gateway integration
- Order ID
- Payment ID
- Signature verification
- Webhook processing
- Payment status reconciliation
- Failed payment handling
- Duplicate payment protection
- Partial payment where applicable
- Refund processing
- Chargeback handling
- Payment receipt
- Invoice
- GST invoice where applicable

Frontend success must never be treated as the final source of payment truth; verified gateway/server-side events must control financial status.

# 52. Financial Ledger

The platform must maintain an auditable financial ledger separate from display balances.

### Ledger Entry

Every financial entry should contain:

- Transaction ID
- Reference ID
- User
- Teacher
- Student
- Amount
- Credit/debit
- Transaction type
- Balance before
- Balance after
- Currency
- Status
- Timestamp
- Related booking/package/payment
- Created by/system source

### Example

```text
Student Payment          +₹5,000
Gateway Fee               -₹100
Platform Commission       -₹750
Teacher Earning          +₹4,150
Escrow                   ₹4,150
Released                 ₹4,150
```

Financial ledger records must be immutable or protected from unauthorized modification.

# 53. Cancellation & Refund Policy Engine

Cancellation and refund rules should be configurable.

### Supported Scenarios

- Student cancellation
- Teacher cancellation
- Late cancellation
- Student no-show
- Teacher no-show
- Technical failure
- Rescheduling
- Dispute
- Package cancellation

### Configurable Rules

Admin should configure:

- Cancellation window
- Refund percentage
- Teacher compensation
- Platform commission adjustment
- Gateway fee treatment
- Late cancellation penalty
- No-show treatment
- Automatic vs manual refund

### Example

```text
Student cancels 24+ hours before
→ 100% refund

Student cancels shortly before class
→ Policy-based partial refund

Teacher cancels
→ Student refund + applicable teacher penalty

Technical issue
→ Admin/policy-based resolution
```

# 54. Teacher Matching & Recommendation Engine

The platform should support intelligent teacher recommendations in addition to manual search.

### Matching Inputs

- Subject
- Class/grade
- Board
- Learning goal
- Budget
- Language
- Location/timezone
- Teaching mode
- Availability
- Experience
- Rating
- Reviews
- Teacher verification
- Student preferences

### Example

```text
Student Requirement
↓
Matching Engine
↓
Teacher A — 94% Match
Teacher B — 91% Match
Teacher C — 87% Match
```

The matching algorithm and weights should be configurable and measurable.

# 55. Teacher Marketplace Ranking

Teacher listing order should be determined by a configurable ranking system.

### Ranking Signals

- Relevance to search
- Verification status
- Rating
- Review quality/volume
- Profile completion
- Experience
- Availability
- Response rate
- Demo conversion
- Student retention
- Cancellation rate
- Student satisfaction
- New-teacher promotion
- Featured/sponsored status

Admin should be able to configure ranking weights and marketplace rules.

# 56. Anti-Bypass & Marketplace Protection

Because students and teachers meet through the platform, the platform must reduce off-platform transactions.

### Protection Features

- Phone number detection in chat
- Email detection
- UPI/payment ID detection
- External payment-link detection
- Suspicious-message detection
- User warnings
- Report functionality
- Account penalties
- Transaction-protection messaging
- Authorized communication controls

Rules must respect applicable privacy and legal requirements.

# 57. Communication Moderation

The communication system should support safe and controlled interaction.

### Features

- One-to-one chat
- File sharing
- Read/unread status
- Message reporting
- Block user
- Report teacher/student
- Attachment validation
- Suspicious link detection
- Message history
- Admin escalation
- Communication retention rules

# 58. Support & Ticket Management

The platform should have a centralized customer support system.

### Support Categories

- Student support
- Parent support
- Teacher support
- Booking support
- Payment support
- Refund support
- Technical support
- Dispute support
- Account support

### Ticket Lifecycle

```text
Created
↓
Assigned
↓
In Progress
↓
Waiting for User
↓
Resolved
↓
Closed
```

### Ticket Features

- Ticket number
- Category
- Priority
- Assigned support agent
- Conversation
- Attachments
- Internal notes
- Resolution
- SLA tracking
- User satisfaction rating

# 59. Coupon, Promotion & Referral System

### Coupons

Support:

- Percentage discount
- Fixed discount
- First-booking discount
- First-demo promotion
- Teacher-specific coupon
- Subject-specific coupon
- Campaign coupon
- Expiry date
- Usage limit
- Per-user usage limit
- Minimum order value

### Referral

Support:

- Student referral
- Teacher referral
- Affiliate referral
- Referral code
- Reward rules
- Reward eligibility
- Referral tracking
- Abuse prevention

# 60. Teacher Performance Management

The platform should measure teacher performance.

### Metrics

- Profile views
- Demo bookings
- Demo attendance
- Demo conversion
- Classes completed
- Student retention
- Rating
- Review count
- Response rate
- Cancellation rate
- No-show rate
- Earnings

A configurable Teacher Performance Score may be used for ranking, quality control and teacher coaching.

# 61. Waitlist Management

When a teacher or class is fully booked:

```text
Class/Teacher Full
↓
Student Joins Waitlist
↓
Slot Becomes Available
↓
Eligible Students Notified
↓
Booking Window
↓
Booking Confirmed
```

Admin should be able to configure waitlist priority and notification rules.

# 62. Subscription & Recurring Payments

Where recurring plans are offered, the platform should support:

- Monthly subscriptions
- Recurring packages
- Auto-renewal
- Renewal reminders
- Failed renewal
- Grace period
- Cancellation
- Pause/resume
- Renewal payment history
- Subscription status

# 63. CMS & Public Content Management

Admin should manage public content without developer intervention.

### CMS

- Homepage sections
- Banners
- FAQs
- Blog
- Testimonials
- Static pages
- About Us
- Contact information
- Terms & conditions
- Privacy policy
- Refund policy
- Teacher agreement
- Student/parent policies
- Subject pages
- Class pages
- Location pages

# 64. SEO Management

The marketplace should support programmatic SEO.

### SEO Pages

Examples:

```text
/teachers
/teachers/maths
/teachers/maths/class-10
/teachers/cbse/maths
/teachers/bhubaneswar/maths
```

### SEO Features

- Dynamic page title
- Meta description
- Canonical URL
- Structured data/schema
- Sitemap
- Robots configuration
- SEO-friendly URLs
- Teacher profile indexing rules
- Subject landing pages
- Class landing pages
- Location landing pages

# 65. Notification Engine

The platform should use a centralized notification engine.

### Channels

- In-app
- Push notification
- WhatsApp
- SMS
- Email

### Notification Management

- Template management
- Variables/placeholders
- Language
- Transactional vs marketing notification
- Delivery status
- Retry
- Failure logs
- User preferences
- Notification history

# 66. Microsoft Teams OAuth & Integration Management

The Teams integration should support secure account/integration management.

### Requirements

- Microsoft OAuth
- Access token management
- Refresh token handling
- Meeting creation
- Meeting update
- Meeting cancellation
- Meeting link storage
- Timezone handling
- Permission error handling
- Token expiry handling
- Teacher disconnect/reconnect

The platform must maintain:

```text
Platform Class ID
        ↓
Teams Meeting
        ↓
Teacher
        ↓
Student/Parent
```

# 67. Safety, Child Protection & Trust

Because the platform may involve minors, dedicated safety controls are required.

### Features

- Parent consent where applicable
- Child account controls
- Teacher code of conduct
- Student code of conduct
- Communication restrictions
- Report abuse
- Block user
- Safety escalation
- Suspicious behavior review
- Admin investigation
- Evidence/audit trail

# 68. Fraud & Abuse Prevention

The platform should monitor suspicious activity.

### Fraud Areas

- Multiple accounts
- Fake teacher profiles
- Fake reviews
- Coupon abuse
- Referral abuse
- Payment fraud
- Chargebacks
- Suspicious login
- Account takeover
- Excessive cancellations
- Marketplace manipulation

### Controls

- Rate limits
- Risk scoring
- Device/session signals
- Account verification
- Payment verification
- Manual review
- Automated alerts
- Account suspension

# 69. Review Moderation

Reviews should be controlled by eligibility and moderation rules.

### Features

- Verified-class review
- Review eligibility
- Review submission
- Teacher response
- Report review
- Admin moderation
- Review edit policy
- Review deletion policy
- Fake-review detection
- Rating recalculation

# 70. Product Analytics & Business Intelligence

In addition to CRM activity tracking, the platform should provide a complete product funnel.

### Core Funnel

```text
Visitor
↓
Search
↓
Teacher View
↓
Signup
↓
Demo Booking
↓
Demo Attendance
↓
Package Purchase
↓
First Class
↓
Repeat Purchase
↓
Retention
```

### KPIs

- Visitors
- Signup conversion
- Demo conversion
- Demo attendance rate
- Payment conversion
- First-class conversion
- Repeat purchase rate
- Student retention
- Teacher retention
- CAC
- LTV
- GMV
- Platform revenue
- Take rate
- Refund rate
- Cancellation rate
- No-show rate

# 71. Advanced Sales CRM

The existing Sales CRM should be extended with:

- Calling integration
- WhatsApp integration
- Email integration
- Call activity
- Follow-up reminders
- Automatic lead assignment
- Round-robin assignment
- Sales targets
- Daily call targets
- Conversion targets
- Revenue targets
- Sales leaderboard
- Lost reasons
- Competitor/lost-to-competitor reason
- Re-engagement campaigns

# 72. Data Privacy & Consent Management

The platform must provide appropriate controls for personal data.

### Features

- Consent management
- Cookie consent
- Marketing opt-in/opt-out
- Data export
- Account deletion
- Data retention rules
- Data anonymization
- Access logs
- Restricted sensitive-data access
- Privacy preference management

All tracking and data processing must follow applicable privacy and data-protection requirements.

# 73. Non-Functional Requirements

### Performance

The platform should define measurable targets for:

- Page-load performance
- API response time
- Search response time
- Booking response time
- Payment confirmation

### Scalability

The architecture should be capable of scaling from initial MVP usage to significantly larger user, booking and transaction volumes.

### Availability

- High availability target
- Automated backups
- Disaster recovery
- Database recovery procedures
- Service health monitoring

### Security

- Secure password hashing
- Authentication/session security
- Role-based access control
- Two-factor authentication where required
- API authentication
- Rate limiting
- Encryption in transit
- Encryption for sensitive stored data
- Secure file uploads
- Security monitoring

### Monitoring

- Application errors
- API failures
- Database health
- Payment failures
- Notification failures
- Teams integration failures
- Server health
- Suspicious activity

# 74. Acceptance Criteria

Every major module should have measurable acceptance criteria before development is considered complete.

### Example: Demo Booking

```text
Given a verified teacher has an available slot

When a student selects the slot

Then the system temporarily locks the slot

And prevents another student from booking it

And completes required payment/confirmation

And creates the booking

And updates teacher/student calendars

And sends confirmation

And schedules reminders
```

### Example: Payment

```text
Given a student starts a payment

When the gateway confirms successful payment through a verified server-side event

Then the payment becomes Paid

And the booking is confirmed

And the financial ledger is updated

And the applicable escrow record is created

And the student receives a receipt
```

### Example: Teacher Payout

```text
Given a completed eligible class

When the platform release rules are satisfied

Then the eligible teacher amount becomes Available

And the teacher can request payout according to payout rules

And the payout transaction is recorded in the financial ledger
```

All modules should have similar acceptance criteria covering success, failure, cancellation and edge cases.

# 75. Expanded Admin Modules

The Admin Panel should additionally include:

- CMS management
- Coupon management
- Referral management
- Notification templates
- Support tickets
- Fraud management
- Review moderation
- Teacher ranking configuration
- Teacher matching configuration
- Commission rules
- Refund rules
- Cancellation rules
- Tax settings
- Payment gateway settings
- Teams integration settings
- Feature flags
- System configuration
- Maintenance mode
- Data/privacy controls

# 76. Recommended Production Architecture

The platform should be treated as a set of connected business engines rather than only separate dashboards.

```text
                    EDUCATION PLATFORM
                           │
       ┌───────────────────┼────────────────────┐
       │                   │                    │
   MARKETPLACE          LEARNING              CRM
       │                   │                    │
 Discovery             Classes              Leads
 Search                Attendance            Sales
 Matching              LMS                  Follow-up
 Profiles              Packages             User 360
 Reviews               Progress             Analytics
 Booking
       │                   │                    │
       └───────────────────┼────────────────────┘
                           │
                    BOOKING ENGINE
                           │
              ┌────────────┼────────────┐
              │            │            │
           Calendar     Scheduling   Availability
              │            │            │
              └────────────┼────────────┘
                           │
                    PAYMENT ENGINE
                           │
             Payment → Ledger → Escrow
                           │
                    Payout / Refund
                           │
       ┌───────────────────┼───────────────────┐
       │                   │                   │
 Communication        Notification          Support
       │                   │                   │
 Chat/WhatsApp       SMS/Email/Push         Tickets
                           │
                     TRUST & SAFETY
                           │
              Fraud / Moderation / KYC
                           │
                       ADMIN / CMS
                           │
              Reports / Audit / Settings
```

# 77. Updated MVP Prioritization

### Phase 1 — Core Marketplace

- User registration/login
- Student
- Parent
- Teacher
- Teacher onboarding
- Teacher verification
- Teacher profiles
- Search
- Filters
- Teacher ranking
- Student requirements
- Teacher listing
- Teacher profile
- Teacher matching
- Demo booking
- Availability engine
- Calendar

### Phase 2 — Booking & Learning

- Class booking
- Packages
- Package lifecycle
- One-to-one classes
- Group classes
- Recurring classes
- Microsoft Teams
- Attendance
- Learning materials
- Communication
- Waitlist

### Phase 3 — Financial

- Payment gateway
- Payment verification/webhooks
- Financial ledger
- Escrow
- Commission
- Refund engine
- Cancellation rules
- Teacher balance
- Payout
- Invoice/receipt
- Financial reports

### Phase 4 — CRM & Sales

- Visitor tracking
- Signup tracking
- User activity
- Search history
- Interest tracking
- User 360
- Lead management
- Lead scoring
- Sales assignment
- Follow-up
- Calling/WhatsApp integration
- Sales reports

### Phase 5 — Trust, Support & Operations

- Support tickets
- Dispute management
- Fraud detection
- Review moderation
- Anti-bypass controls
- Safety controls
- Teacher performance
- Referral system
- Coupon system

### Phase 6 — Growth & Platform

- CMS
- SEO
- Product analytics
- Advanced reports
- Notification engine
- Subscription/recurring payments
- Referral/affiliate
- Advanced matching
- Advanced marketplace ranking
- Feature flags
- Complete admin configuration
- Data/privacy management
- Advanced audit

# 78. Final Product Definition — Updated

The platform is a complete digital education marketplace where students and parents discover verified teachers, define their learning requirements, compare and match with teachers, book demos, purchase class packages, schedule and attend live lessons, communicate with teachers, access learning materials and monitor learning progress.

Teachers receive students through the marketplace rather than purchasing leads. They manage their profiles, availability, students, demos, classes, learning content, earnings and payouts.

The platform owns the complete commercial and operational journey:

```text
DISCOVERY
    ↓
SEARCH / MATCHING
    ↓
TEACHER MARKETPLACE
    ↓
DEMO
    ↓
TEACHER SELECTION
    ↓
PACKAGE / CLASS BOOKING
    ↓
PAYMENT
    ↓
FINANCIAL LEDGER
    ↓
ESCROW
    ↓
CALENDAR / SCHEDULING
    ↓
MICROSOFT TEAMS CLASS
    ↓
ATTENDANCE / LEARNING
    ↓
COMPLETION
    ↓
REFUND / DISPUTE RULES IF REQUIRED
    ↓
TEACHER PAYOUT
    ↓
PARENT / STUDENT MONITORING
    ↓
RETENTION / REPEAT PURCHASE
    ↓
ADMIN AUDIT + SALES + PRODUCT ANALYTICS
```

The key business differentiation remains:

**You don't sell leads.**

**You own the transaction, booking and learning journey.**

---

# 9. Data & Entity Model Baseline

The implementation should define a normalized domain model around the following core entities.

### Identity
- User
- Role
- Permission
- Session
- Consent

### Marketplace
- Teacher
- Teacher Verification
- Subject
- Class/Grade
- Board
- Category
- Teacher Package
- Teacher Availability
- Teacher Ranking
- Match Result

### Student
- Student Profile
- Parent
- Child Relationship
- Student Requirement Profile

### Booking & Learning
- Demo
- Booking
- Recurring Schedule
- Class
- Attendance
- Assignment
- Submission
- Learning Material
- Class Note

### Communication
- Conversation
- Participant
- Message
- Attachment
- Notification

### Finance
- Payment
- Payment Event
- Ledger Entry
- Escrow
- Commission
- Refund
- Dispute
- Payout
- Invoice

### CRM
- Visitor
- Session
- Lead
- Lead Activity
- Lead Score
- Sales Assignment
- Follow-up
- Campaign
- UTM Attribution

### Trust & Operations
- Review
- Report
- Fraud Risk
- Support Ticket
- Audit Log
- Business Rule
- Rule Version
- Coupon
- Referral

Relationships and ownership must be explicitly defined in the system design before implementation.

---

# 10. State & Lifecycle Requirements

Every major transactional entity must have an explicit state machine.

### Teacher
`Draft → Pending → Under Review → Verified → Published → Suspended → Rejected`

### Demo
`Requested → Confirmed → Scheduled → Started → Completed → Converted / Not Converted → Cancelled`

### Booking
`Slot Held → Pending Payment → Confirmed → Scheduled → Live → Completed`

Alternative terminal states:

`Cancelled / Rescheduled / No-Show / Disputed`

### Payment
`Created → Pending → Paid → Verified → Escrowed → Released`

Alternative states:

`Failed / Cancelled / Refunded / Partially Refunded / Chargeback`

### Payout
`Requested → Validating → Approved → Processing → Paid`

Alternative:

`Rejected / Failed / Reversed`

### Support Ticket
`Created → Assigned → In Progress → Waiting → Resolved → Closed`

All state transitions must be auditable.

---

# 11. API & Event Requirements

The final technical design should expose domain operations through secure APIs/services.

Core operation groups include:

- Authentication
- Teacher onboarding
- Teacher verification
- Marketplace search
- Matching
- Availability
- Slot locking
- Demo booking
- Class booking
- Package management
- Payment initiation
- Payment webhook
- Refund
- Cancellation
- Rescheduling
- Attendance
- Learning content
- Messaging
- Notifications
- Payout
- CRM
- Support
- Admin configuration
- Reporting

Important events should be published/handled reliably, including:

- UserRegistered
- TeacherVerified
- DemoBooked
- DemoCompleted
- BookingConfirmed
- PaymentVerified
- EscrowCreated
- ClassCompleted
- RefundIssued
- PayoutRequested
- PayoutCompleted
- LeadCreated
- LeadConverted
- ReviewSubmitted

Events must be idempotent where duplicate delivery is possible.

---

# 12. Financial Integrity Requirements

Financial operations must be server-authoritative.

### Requirements

- Never trust frontend payment status.
- Verify payment through gateway/server events.
- Use idempotency for financial operations.
- Prevent duplicate ledger entries.
- Maintain immutable/auditable financial records.
- Reconcile gateway transactions.
- Preserve historical commission calculations.
- Preserve refund calculations.
- Prevent unauthorized balance manipulation.

---

# 13. Security Requirements

### Authentication
- Secure password hashing
- Session/token security
- MFA/2FA where required
- Account recovery
- Login protection

### Authorization
- RBAC
- Permission checks server-side
- Sensitive-action authorization
- Financial-operation approval

### Application Security
- Rate limiting
- Secure file uploads
- Input validation
- Output encoding
- API authentication
- Secure secrets management
- Encryption in transit
- Encryption for sensitive stored data

### Monitoring
- Authentication anomalies
- API abuse
- Payment failures
- Suspicious activity
- Administrative changes

---

# 14. Privacy & Data Governance

The platform must implement:

- Consent management
- Cookie preferences
- Marketing opt-in/opt-out
- Data access controls
- Data export
- Account deletion workflow
- Retention policies
- Data anonymization
- Access logs
- Privacy preferences

Tracking of anonymous visitors and user activity must follow applicable laws, policies and consent requirements.

---

# 15. Non-Functional Requirements

### Performance

Final targets must be agreed before development. The system must define measurable targets for:

- Page load
- API response
- Search response
- Booking response
- Payment confirmation
- Notification delivery

### Availability

- Defined uptime target
- Automated backups
- Disaster recovery
- Recovery procedures
- Service health monitoring

### Scalability

Architecture should support growth in:

- Users
- Teachers
- Concurrent searches
- Concurrent bookings
- Classes
- Financial transactions
- Messages
- Analytics events

### Observability

- Centralized logs
- Error tracking
- Metrics
- Health checks
- Payment monitoring
- Notification monitoring
- Integration monitoring
- Alerting

---

# 16. Acceptance Criteria Framework

Every major requirement must contain:

### Given
Initial state and permissions.

### When
User/system action.

### Then
Expected result.

### And
Side effects, notifications, ledger updates, audit entries and related state changes.

Each critical workflow must include:

- Happy path
- Validation failure
- Authorization failure
- Duplicate request
- Timeout
- Cancellation
- Rescheduling
- Refund
- Integration failure
- Recovery/retry

---

# 17. Reporting & KPI Framework

### Marketplace KPIs
- Teacher supply
- Active teachers
- Teacher profile views
- Search volume
- Search-to-profile rate
- Profile-to-demo rate
- Demo conversion
- Booking conversion

### Financial KPIs
- GMV
- Net revenue
- Take rate
- Commission
- Refund rate
- Payout volume
- Escrow balance
- Payment failure rate

### CRM KPIs
- Leads
- Lead source
- Lead score distribution
- Contact rate
- Demo rate
- Conversion rate
- Salesperson performance
- Follow-up SLA

### Learning KPIs
- Classes scheduled
- Classes completed
- Attendance
- No-show rate
- Student retention
- Teacher retention
- Package renewal

### Growth KPIs
- CAC
- LTV
- Repeat purchase
- Referral contribution
- Organic/SEO contribution

---

# 18. Release Strategy

## MVP

The MVP should focus on the minimum transaction loop:

**Teacher onboarding → Marketplace → Search → Profile → Availability → Demo → Booking → Payment → Basic class → Admin → Basic CRM**

## V1

- Packages
- Recurring classes
- Teams
- Attendance
- Parent portal
- Teacher payouts
- Learning materials
- Refund/dispute
- Reviews
- Notifications

## V2

- Matching engine
- Advanced ranking
- Advanced CRM
- Fraud/risk
- Anti-bypass
- Referral/coupons
- Product analytics
- SEO/CMS
- Subscription
- Advanced support

## V3

- Advanced automation
- Advanced personalization
- Advanced BI
- Marketplace optimization
- Additional integrations

---

# 19. Definition of Done

A feature is complete only when:

- Functional requirements are implemented.
- Permission rules are implemented.
- Business rules are configurable where required.
- Validation is implemented.
- Error/edge cases are handled.
- Audit requirements are implemented.
- Notifications are implemented where applicable.
- Analytics events are implemented where applicable.
- Financial operations are reconciled where applicable.
- QA acceptance criteria pass.
- Security checks pass.
- Documentation is updated.

---

# 20. Product Success Definition

The product succeeds when it reliably converts:

**Traffic → Discovery → Teacher Selection → Demo → Paid Booking → Learning → Completion → Retention**

while maintaining:

- High teacher quality
- High student satisfaction
- Strong teacher utilization
- Reliable payment processing
- Low refund/dispute rates
- Strong teacher retention
- Strong student retention
- Healthy platform take rate
- Efficient sales conversion

---

## Final Product Statement

> **A configurable, transaction-led education marketplace where students and parents discover verified teachers, compare and match with them, book demos, purchase classes, attend live lessons, communicate, track learning and renew services — while teachers manage their teaching business and the platform manages marketplace discovery, booking, payments, escrow, payouts, CRM, trust, support, analytics and operational policies.**

**Core differentiation:**

> **We do not sell leads. We own the education transaction and learning journey.**
