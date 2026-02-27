# UI Troubleshooting Guide

## Empty/Blank Screen Issues

### 1. Check Browser Console
Open browser DevTools (F12) and check the Console tab for errors.

### 2. Common Issues:

#### JavaScript Errors
- **Error**: "Cannot find module" or import errors
  - **Solution**: Run `npm install` to ensure all dependencies are installed

#### Router Not Working
- **Error**: Page loads but shows blank
  - **Solution**: Check if routes are properly defined in `src/router/index.ts`

#### API Connection Issues
- **Error**: CORS errors or API not reachable
  - **Solution**: 
    1. Make sure API is running on `http://localhost:8000`
    2. Check `.env` file has `VITE_API_URL=http://localhost:8000/api`
    3. Restart the dev server after changing `.env`

### 3. Quick Fixes:

```bash
# Clear node_modules and reinstall
cd app/ui
rm -rf node_modules package-lock.json
npm install

# Clear browser cache
# Or use incognito/private browsing mode

# Check if port is available
# Vite usually runs on http://localhost:5173
```

### 4. Verify Setup:

1. **Check if Vite is running:**
   - Terminal should show: `Local: http://localhost:5173/`
   - Open that URL in browser

2. **Check if files exist:**
   - `src/main.ts` should exist
   - `src/App.vue` should exist
   - `src/views/Login.vue` should exist

3. **Check browser console:**
   - Open DevTools (F12)
   - Look for red error messages
   - Check Network tab for failed requests

### 5. Manual Test:

1. Open `http://localhost:5173` in browser
2. Open DevTools (F12)
3. Check Console for errors
4. Check Network tab - all requests should be 200 OK
5. If you see 404 errors, the files might not be loading

### 6. Still Not Working?

Try accessing directly:
- `http://localhost:5173/login` - Should show login page
- `http://localhost:5173/dashboard` - Should redirect to login if not authenticated

If still blank, check:
- Is the API running? (`http://localhost:8000/api/health`)
- Are there CORS errors in console?
- Is JavaScript enabled in browser?
