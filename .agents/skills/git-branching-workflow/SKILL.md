---
name: git-branching-workflow
description: "Safe Git branching and deployment workflow for projects with automated deployment (GitHub Actions / cPanel) configured on the main branch. Use when developing features, fixing bugs, creating branches, committing, pushing, and preparing releases to prevent premature or untested code from deploying to production."
---

# Git Branching & Safe Deployment Workflow

## Context & Core Principle

This repository has an automated deployment pipeline configured via GitHub Actions (`.github/workflows/deploy.yml`) that triggers on every push to the `main` branch and immediately executes on the cPanel production server.

**Rule: NEVER push untested, experimental, or work-in-progress code directly to `main`.**

---

## 1. Branch Strategy

Always develop on a dedicated branch rather than committing directly to `main`.

- **Development branch:** `dev` or `staging`
- **Feature branch:** `feature/<nama-fitur>` (e.g., `feature/ekspor-pdf`, `feature/bantuan-sembako`)
- **Bugfix branch:** `fix/<nama-perbaikan>` (e.g., `fix/login-session`, `fix/rupiah-format`)

---

## 2. Standard Workflow

### Step 1: Start from Clean & Updated `main`
```bash
git checkout main
git pull origin main
```

### Step 2: Create and Switch to Working Branch
```bash
git checkout -b dev
# atau:
git checkout -b feature/nama-fitur
```

### Step 3: Develop, Test, and Commit
1. Make code changes in the working branch.
2. Run test suites and code formatting:
   ```bash
   php artisan test --compact
   vendor/bin/pint --format agent
   ```
3. Stage and commit:
   ```bash
   git add .
   git commit -m "feat: deskripsi perubahan yang jelas"
   ```

### Step 4: Push Working Branch (Safe from Production Deploy)
Push the working branch to GitHub. Because the branch name is not `main`, GitHub Actions will **not** trigger deployment to cPanel:
```bash
git push -u origin dev
```

---

## 3. Merging to `main` (Production Release)

Merge into `main` **only** when:
1. Feature/fix is fully implemented.
2. Local tests have passed (`php artisan test`).
3. Code formatting conforms to project standards.

### Preferred: Pull Request via GitHub
1. Open a Pull Request from `dev` / `feature/...` into `main`.
2. Review the diff.
3. Merge the Pull Request.
4. GitHub Actions will automatically deploy to cPanel safely.

### Alternative: Local Merge
```bash
git checkout main
git pull origin main
git merge dev
# Push to main triggers production auto-deployment:
git push origin main
```
