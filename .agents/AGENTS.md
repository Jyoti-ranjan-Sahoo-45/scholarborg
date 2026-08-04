# Scholarb Workspace Rules & Architectural Guide

This document defines the behavior rules for AI coding assistants and explains the architecture of the Scholarb workspace for developers.

---

## 🛡️ AI Agent Rules (Strict Behavioral Guidelines)

Every AI agent loading this workspace **MUST** adhere to the following rules:

### 1. The "Think & Discuss" Plan Enforcement
* **Rule:** Before implementing any code changes, creating new files, or making modifications, the AI **MUST** write a detailed proposal, discuss it in chat, and obtain the user's explicit approval.
* **Exceptions:** None. Even minor edits require a quick confirmation.

### 2. The Change Registry Rule (`changes.md`)
* **Rule:** After every successful deployment or file modification, the AI **MUST** append a summary entry to [changes.md](file:///d:/rocket_lms_v2.2.rar/scholarborg/changes.md). The entry must include:
  - Date & Time
  - Files modified
  - Summary of the changes and why they were made.

### 3. Deploy Verification Rule
* **Rule:** After deploying changes via the Python script, the AI **MUST** verify the status of both environments by fetching headers:
  - Rocket LMS root: `https://scholarb.org`
  - ERPGo CRM portal: `https://scholarb.org/crm`
  - Both must return `200 OK`.

### 4. Deploy Revert Mechanism
* **Rule:** If a deployment causes any unexpected issues, the AI **MUST** immediately roll back using `python deploy.py revert` to restore the previous backup checkpoint.

---


## 📁 Workspace Architecture

This folder contains a unified setup for both the client portal and the CRM:

```text
scholarborg/
├── .agents/          # Custom AI agent guidelines (This folder)
├── crm/              # Local ERPGo CRM codebase (Laravel)
├── lms/              # Local Rocket LMS codebase (Laravel)
├── config.json       # SSH & Database Configuration
├── deploy.py         # Smart Python Deployment Script
├── changes.md        # Registry of all code modifications
└── README.md         # Developer Manual
```

### 1. Rocket LMS (Root Domain: `scholarb.org`)
* **Database:** `u181357068_Eduction2`
* **Core Logic:** A customized Laravel-based learning platform.
* **Custom Integrations:** Bypasses local DRM checks and validates licenses against the CRM API located under `scholarb.org/crm/api/validate-license`.

### 2. ERPGo CRM (Subdirectory: `scholarb.org/crm`)
* **Database:** `u181357068_Serp1`
* **Core Logic:** ERP and SaaS management system.
* **Key Feature:** Exposes the API endpoint `https://scholarb.org/api/validate-license` which queries the `software_licenses` table to validate purchase codes for the LMS main script, plugins bundle, and theme builder.
