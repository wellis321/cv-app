# Fix npm Cache Permission Error

## The Problem

You're seeing this error:
```
npm error EACCES: permission denied, mkdir '/Users/wellis/.npm/_cacache/...'
npm error Your cache folder contains root-owned files
```

This happens when npm cache files were created with root permissions (usually from running npm with sudo).

## Solution

Run this command in your terminal:

```bash
sudo chown -R $(id -u):$(id -g) ~/.npm
```

This will:
- Change ownership of all files in `~/.npm` to your user
- Fix the permission issues preventing npm from working

**After running the command:**
1. Enter your password when prompted
2. Wait for it to complete
3. Restart Cursor
4. The MCP browser server should now work

## Alternative: Clear npm Cache

If you prefer to clear the cache instead:

```bash
npm cache clean --force
```

Then try again. However, fixing permissions is the better long-term solution.

## Verify It Worked

After fixing permissions, verify:

```bash
npm cache verify
```

This should complete without errors.

## Then Restart Cursor

After fixing the npm permissions:
1. Completely quit Cursor (Cmd+Q)
2. Restart Cursor
3. Check MCP Servers settings - "browsermcp" should now connect successfully


