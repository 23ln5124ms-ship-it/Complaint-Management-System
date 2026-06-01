<#
PowerShell script to perform the feature/reports-api workflow with validation and safe checks.
Usage examples:
.\scripts\feature-reports-api.ps1 -RepoUrl "https://github.com/USERNAME/reponame.git" -PersonName "Person3 Name" -PersonEmail "person3@email.com"
OR
.\scripts\feature-reports-api.ps1 -GitHubUser "USERNAME" -RepoName "complainhub" -PersonName "Person3 Name" -PersonEmail "person3@email.com"
#>
param(
    [string]$RepoUrl = '',
    [string]$GitHubUser = 'rodriguezamarieanthonette2004-glitch',
    [string]$RepoName = 'complainhub',
    [string]$LocalPath = '',
    [string]$PersonName = 'Marie Anthonette',
    [string]$PersonEmail = 'rodriguezamarieanthonette2004@email.com',
    [switch]$UseCurrentDirectory
)

function Abort($msg){ Write-Host "ERROR: $msg" -ForegroundColor Red; exit 1 }

function IsDirectoryEmpty($path) {
    return @(Get-ChildItem -Force -LiteralPath $path | Where-Object { $_.Name -ne '.' -and $_.Name -ne '..' }).Count -eq 0
}

function GetOriginUrl() {
    $url = git remote get-url origin 2>$null
    if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($url)) { return '' }
    return $url.Trim()
}

function CloneRepository($url, $path) {
    Write-Host "Cloning $url into $path..."
    git clone $url $path
    if ($LASTEXITCODE -ne 0) { Abort "`nGit clone failed. Verify the repo URL and network access.`nIf the repo is private, authenticate first (git credential manager or SSH)." }
}

function ConstructRepoUrl() {
    if (-not $RepoUrl) {
        if (-not $GitHubUser) { Abort "Provide either -RepoUrl or -GitHubUser." }
        $resolvedUrl = "https://github.com/$GitHubUser/$RepoName.git"
        $script:RepoUrl = $resolvedUrl
    }
}

function ConfigureExistingGitRepo($path) {
    Set-Location $path
    git rev-parse --is-inside-work-tree > $null 2>&1
    if ($LASTEXITCODE -ne 0) {
        ConstructRepoUrl
        Write-Host "Directory '$path' exists but is not a git repository. Initializing and configuring origin..."
        git init > $null 2>&1
        git remote add origin $RepoUrl > $null 2>&1
        git fetch origin --depth=1 > $null 2>&1
        if ($LASTEXITCODE -ne 0) { Abort "Failed to fetch from remote. Verify the RepoUrl and network access." }
        git checkout -b main origin/main 2>$null
        if ($LASTEXITCODE -ne 0) {
            git checkout -b master origin/master 2>$null
            if ($LASTEXITCODE -ne 0) { Abort "Failed to checkout a remote branch from origin. Ensure the repository has a main or master branch." }
        }
        $RepoUrl = GetOriginUrl
        Write-Host "Initialized git repository and set origin to $RepoUrl" -ForegroundColor Green
    } else {
        $existingOrigin = GetOriginUrl
        if (-not $existingOrigin) {
            ConstructRepoUrl
            git remote add origin $RepoUrl > $null 2>&1
            Write-Host "Added origin remote $RepoUrl to existing git repository." -ForegroundColor Green
        } else {
            Write-Host "Existing git repository already has origin: $existingOrigin" -ForegroundColor Green
        }
        Set-Location $path
    }
}

# Determine where to operate
if ($UseCurrentDirectory) {
    $targetDir = (Get-Location).Path
    Write-Host "Using current directory: $targetDir"
    git rev-parse --is-inside-work-tree > $null 2>&1
    if ($LASTEXITCODE -ne 0) { Abort "Current directory is not a git repository. Run the script inside a cloned repo or provide -RepoUrl." }
    $RepoUrl = GetOriginUrl
    if (-not $RepoUrl) { Abort "Cannot determine repo URL from current git repository. Provide -RepoUrl or ensure 'origin' remote exists." }
} elseif ($LocalPath) {
    $targetDir = (Resolve-Path $LocalPath).Path
    if (-not (Test-Path $targetDir)) { New-Item -ItemType Directory -Path $targetDir | Out-Null }
    Write-Host "Using local path: $targetDir"
    ConfigureExistingGitRepo $targetDir
    if (-not $RepoUrl) { Abort "Cannot determine repo URL from local git repository. Provide -RepoUrl or ensure 'origin' remote exists." }
} else {
    ConstructRepoUrl
    $targetDir = [System.IO.Path]::GetFileNameWithoutExtension($RepoUrl)
    if (Test-Path $targetDir) {
        Write-Host "Directory '$targetDir' already exists. Will use existing folder." -ForegroundColor Yellow
        ConfigureExistingGitRepo $targetDir
    } else {
        CloneRepository $RepoUrl $targetDir
        Set-Location $targetDir
    }
}

Write-Host "Target repo URL: $RepoUrl"
Write-Host "Target directory: $targetDir"

# Configure local git identity
git config user.name "$PersonName"
if ($LASTEXITCODE -ne 0) { Abort "Failed to set git user.name" }

git config user.email "$PersonEmail"
if ($LASTEXITCODE -ne 0) { Abort "Failed to set git user.email" }

# Create feature branch
$branch = 'feature/reports-api'
Write-Host "Creating and switching to branch '$branch'..."
# If branch exists remotely, create tracking branch; otherwise create new
$existing = git branch --list $branch
if ($existing) {
    git checkout $branch
} else {
    git checkout -b $branch
}
if ($LASTEXITCODE -ne 0) { Abort "Failed to create/switch to branch '$branch'" }

function SafeAddCommit([string]$paths, [string]$message) {
    # Only add if files exist
    $exists = $false
    foreach ($p in $paths -split '\s+') {
        if (Test-Path $p) { $exists = $true; break }
    }
    if (-not $exists) {
        Write-Host "Skipping commit: no paths found for '$message'" -ForegroundColor Yellow
        return
    }

    git add $paths
    if ($LASTEXITCODE -ne 0) { Write-Host "git add failed for: $paths" -ForegroundColor Red; return }

    # Only commit if there are staged changes
    $status = git diff --cached --name-only
    if (-not $status) { Write-Host "No staged changes for '$message', skipping commit."; return }

    git commit -m $message
    if ($LASTEXITCODE -ne 0) { Write-Host "git commit failed for: $message" -ForegroundColor Red; return }
    Write-Host "Committed: $message" -ForegroundColor Green
}

# Commit 1 — Admin controllers
SafeAddCommit 'app/Http/Controllers/Admin/' 'Add: admin complaint controller, category CRUD, report controller'

# Commit 2 — REST API
SafeAddCommit 'app/Http/Controllers/Api/ routes/api.php' 'Add: RESTful API with GET, POST, PUT, DELETE endpoints'

# Commit 3 — Reports & Export
SafeAddCommit 'app/Exports/ resources/views/admin/reports/' 'Add: PDF, CSV, XLSX, JSON export for complaint reports'

# Commit 4 — Seeder & Factory
SafeAddCommit 'database/seeders/ database/factories/' 'Add: database seeders and factories for testing data'

# Commit 5 — Routes
SafeAddCommit 'routes/web.php' 'Add: web routes with auth, user, and admin middleware protection'

# Push branch
Write-Host "Pushing branch '$branch' to origin..."
git push -u origin $branch
if ($LASTEXITCODE -ne 0) { Abort "git push failed. Ensure you have permission to push to the remote." }

Write-Host "Done. Open a Pull Request on GitHub to merge '$branch' into 'main'." -ForegroundColor Green
