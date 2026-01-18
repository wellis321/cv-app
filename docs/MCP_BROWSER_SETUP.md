# MCP Browser Extension Setup Guide

## Issue: "No server info found" Error

This error means Cursor can't find the MCP browser server configuration.

## Step-by-Step Setup

### 1. Verify Node.js Installation

**CRITICAL**: Node.js must be installed directly on your system (not just in WSL or containers).

1. Open Terminal
2. Run: `node --version` and `npm --version`
3. If not installed, download from [nodejs.org](https://nodejs.org/)
4. Make sure Node.js is in your system PATH

### 2. Set Up the Browser Extension

Follow the [official setup guide](https://docs.browsermcp.io/set-up-extension):

1. Open Chrome/Edge
2. Install the Browser MCP extension from the Chrome Web Store
3. After installation, **click the "Connect" button** in the extension popup
   - This is critical - the extension won't work until you click Connect!
4. Make sure the extension is enabled in your browser
5. The extension should show a "Connected" status

### 3. Configure MCP Server in Cursor

#### Step 1: Open Cursor Settings

1. Open Cursor
2. Go to **Settings** (Cmd+, on Mac, Ctrl+, on Windows)
3. Navigate to **Tools & Integrations** → **MCP Servers**

#### Step 2: Add/Configure Browser MCP Server

According to the [official Browser MCP documentation](https://docs.browsermcp.io/), the configuration should be:

```json
{
  "mcpServers": {
    "browsermcp": {
      "command": "npx",
      "args": ["@browsermcp/mcp"]
    }
  }
}
```

**Important Notes:**
- Server name should be `"browsermcp"` (not "browser")
- **DO NOT** use `@browsermcp/mcp@latest` - remove `@latest` if present
- The `-y` flag is optional (npx will prompt if needed)

**If you see "Client closed" error**, the issue is often the `@latest` suffix. Make sure your config uses:
- ✅ `"args": ["@browsermcp/mcp"]` 
- ❌ NOT `"args": ["@browsermcp/mcp@latest"]`

### 4. Enable the Server

1. In Cursor's MCP Servers settings
2. Find the **"browsermcp"** server in the list (note: it's "browsermcp", not "browser")
3. Make sure it's **Enabled** (toggle should be ON)
4. If it shows as disabled or not running, click the **Refresh** or **Reload** button
5. Wait for it to show as "Running" or "Connected"

### 5. Verify Extension Connection

1. Make sure the browser extension is running
2. **Click "Connect" button** in the extension popup (if you haven't already)
3. Check the extension's popup/options for connection status
4. The extension should show it's connected to Cursor

### 6. Restart Cursor

After configuring:
1. **Completely quit Cursor** (Cmd+Q on Mac, or close all windows and quit from menu)
2. Wait a few seconds
3. Restart Cursor
4. Check if the MCP server appears in the status bar or MCP panel

### 7. Check MCP Server Status

1. In Cursor, look for MCP status indicator (usually in status bar at bottom)
2. Or open Command Palette (Cmd+Shift+P / Ctrl+Shift+P) and search for "MCP"
3. Check if the browser server shows as "Connected" or "Running"
4. View server logs if available

## Troubleshooting

### Error: "No server info found"

**This is your current error!** Here's how to fix it:

**Step 1: Verify MCP Server is Configured**
1. Go to Cursor Settings → Tools & Integrations → MCP Servers
2. Check if **"browsermcp"** server is listed (note the exact name)
3. If not, add it using the configuration above (server name must be "browsermcp")
4. Make sure it's **Enabled** and shows as "Running"

**Step 2: Check Configuration Format**
- Remove `@latest` from package name if present
- Use: `@browsermcp/mcp` (NOT `@browsermcp/mcp@latest`)
- Verify JSON syntax is correct (no trailing commas, proper quotes)

**Step 3: Verify Node.js**
- Run `which node` (Mac/Linux) or `where node` (Windows)
- Should show a path, not "command not found"
- If missing, install Node.js from nodejs.org

**Step 4: Restart Everything**
1. Quit Cursor completely
2. Close browser
3. Restart browser
4. Click "Connect" in browser extension
5. Restart Cursor

### Error: "Client closed" or "Server not responding"

**Possible causes:**
1. Extension not connected (didn't click "Connect")
2. Node.js not in PATH
3. Port conflict
4. Firewall blocking connection

**Solutions:**
1. **Click "Connect" button in browser extension popup**
2. Check extension is active in browser (not disabled)
3. Check browser console (F12) for extension errors
4. Verify Node.js is accessible: `npx --version`
5. Check if firewall is blocking localhost connections

### Finding the Correct Configuration

The exact configuration depends on which browser MCP extension you're using:

1. **Official Cursor Browser Extension**: Check Cursor's documentation
2. **Third-party MCP Browser Server**: Check that project's README
3. **Custom MCP Server**: Use the server's configuration instructions

## Alternative: Check Extension Logs

The extension logs you showed indicate the server isn't being found. Check:

1. **Cursor MCP Logs**: 
   - Open Command Palette
   - Search for "MCP: Show Logs" or "MCP: View Logs"

2. **Extension Logs**:
   - Open browser DevTools (F12)
   - Check Console for extension errors
   - Check Network tab for connection attempts

## Quick Test

To verify MCP is working:
1. Try using a browser-related command in Cursor
2. Check if browser tools are available in the tool list
3. Look for browser MCP resources in the MCP panel

## Official Documentation

For the most up-to-date instructions, refer to the official Browser MCP documentation:
- **Main Docs**: https://docs.browsermcp.io/
- **Setup Guide**: https://docs.browsermcp.io/set-up-mcp-server
- **Extension Setup**: https://docs.browsermcp.io/set-up-extension
- **Troubleshooting**: https://docs.browsermcp.io/troubleshooting

## Still Not Working?

If it's still not connecting after following the official docs:
1. Check Node.js version (should be 18+)
2. Verify you're using the latest version of Cursor
3. Check the [official troubleshooting guide](https://docs.browsermcp.io/troubleshooting)
4. Try removing and re-adding the MCP server configuration
5. Check Cursor's developer console (View → Developer → Toggle Developer Tools) for errors

