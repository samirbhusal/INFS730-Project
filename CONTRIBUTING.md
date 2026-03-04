# 🛠 Contributing Guide

To keep our project stable, the **main** branch is protected. You cannot push code directly to it. Please follow these 5 simple steps to contribute.

---

### 1. Update your local code

Before starting, ensure you have the latest version of the project:

```bash
git checkout main
git pull origin main
```

### 2. Update your local code

Never work directly on main. Create a new branch for your specific task:

```bash
git checkout -b your-branch-name
```

(Example: git checkout -b add-login-button or git checkout -b fix-header)

### 3. Save your changes

Work on your code as usual, then save your progress to Git:

```bash
git add .
git commit -m "Briefly explain what you changed"
```

### 4. Upload your branch to GitHub

Send your branch to the server and "link" it for easier future pushes:

```bash
git push -u origin your-branch-name
```

### 5. Open a Pull Request (PR)

Go to this repository on GitHub.com.

Click the yellow button that says "Compare & pull request".

Click "Create pull request".

The Rule: At least one teammate must review your code and click "Approve".

Once approved, the Merge button will turn green. Click it to finish!
