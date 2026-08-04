# Scholarb.org Local Workspace & Deployment Manual

Welcome to your unified workspace for **Scholarb.org**! This folder contains local versions of both the **Rocket LMS** and the **ERPGo CRM** codebases, along with a smart Python deployment script to sync local changes to the live Hostinger production server.

---

## 📁 Directory Structure

```text
scholarborg/
├── crm/              # Local ERPGo CRM codebase (Laravel)
├── lms/              # Local Rocket LMS codebase (Laravel)
├── config.json       # SSH & Database Configuration
├── deploy.py         # Smart Python Deployment Script
└── README.md         # This manual
```

---

## 🔑 Server & Credentials Registry

### 🖥️ Hostinger SSH connection settings
* **Host/IP:** `89.117.157.69`
* **Port:** `65002`
* **Username:** `u181357068`
* **Password:** `Jyoti@45r`
* **Root Domain:** `scholarb.org`

### 🗄️ Database Credentials

#### 1. Rocket LMS Database (Live at scholarb.org)
* **Host:** `127.0.0.1` (or `localhost`)
* **DB Name:** `u181357068_Eduction2`
* **Username:** `u181357068_Eduction2`
* **Password:** `Eduction2@1234`

#### 2. ERPGo CRM Database (Live at scholarb.org/crm)
* **Host:** `127.0.0.1` (or `localhost`)
* **DB Name:** `u181357068_Serp1`
* **Username:** `u181357068_Serp1`
* **Password:** `Serp1@1234`

---

## 🚀 How to Deploy Changes

The included `deploy.py` is a smart deployment tool. It compares your local files with the remote server's files based on size and modification timestamps, and uploads **only new or modified files**.

It automatically excludes runtime/dependency folders (like `vendor`, `node_modules`, `storage/logs`, `.git`, and media uploads `public/store`), making deployments take under 1 second!

### 1. Prerequisites
Ensure you have the SSH library `paramiko` installed locally:
```bash
pip install paramiko
```

### 2. Deploy Commands

You can run `deploy.py` using Python by passing the target name (`lms`, `crm`, or `both`):

#### 🔍 Dry Run (Check what would be uploaded first)
It is highly recommended to run a dry-run before doing a real deployment to preview the files that will be synced:
```bash
python deploy.py lms --dry-run
python deploy.py crm --dry-run
```

#### 🚢 Real Deployments
Upload changes directly to production:
* **Deploy Rocket LMS changes:**
  ```bash
  python deploy.py lms
  ```
* **Deploy ERPGo CRM changes:**
  ```bash
  python deploy.py crm
  ```
* **Deploy both at the same time:**
  ```bash
  python deploy.py both
  ```

#### ↩️ Reverting Checkpoints (Rollback changes)
Every successful deployment automatically creates a checkpoint backup containing the files that were overwritten. You can revert any past deployment by running:
```bash
python deploy.py revert
```
This will present a numbered list of all past deployments (most recent at the top). Enter the number of the deployment you wish to roll back (e.g. `1` for last deployment, `2` for previous deployment).

---


## 💡 Guidelines & Best Practices
1. **Local Changes:** Only edit code inside `lms/` or `crm/`. The deployment script will sync your changes.
2. **Exclusions:** Do not copy or add third-party dependencies to the local folders; let Composer or NPM manage them.
3. **Database Changes:** If you perform SQL alterations, make sure to execute them directly in Hostinger's phpMyAdmin or via your local DB administration tools using the database credentials listed above.

---

## 🤖 AI Agent Workflow Rules (Important for AI Assistants)
When using an AI coding assistant (like Gemini or other coding agents) in this workspace, the AI must follow these rules defined in `.agents/AGENTS.md`:
1. **Plan Approval:** The AI must discuss any plan and obtain your explicit agreement in chat **before** writing code or modifying any files.
2. **Changes Log:** The AI must log every code modification in the `changes.md` file after a successful deployment.
3. **Verification:** The AI must verify that both `https://scholarb.org` and `https://scholarb.org/crm` are fully online (HTTP 200 OK) after deploying.

