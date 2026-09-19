# Sip It Coffee Shop

Standalone Sip It website with a PHP reservation endpoint and Gmail SMTP support.

## Local run

```powershell
php -S 127.0.0.1:8000
```

Set the SMTP environment variables before starting PHP:

```text
GMAIL_EMAIL=sipitcoffeeshop@gmail.com
GMAIL_APP_PASSWORD=your-gmail-app-password
RESERVATION_EMAIL=sipitcoffeeshop@gmail.com
BLUDIT_SMTP_HOST=smtp.gmail.com
BLUDIT_SMTP_PORT=587
BLUDIT_SMTP_ENCRYPTION=tls
```

Never commit the real app password. For Render, deploy as a Docker Web Service and add these values as environment variables in the Render dashboard.
