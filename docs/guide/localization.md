# Localization

Publish the language files and translate them to localise the UI:

```bash
php artisan vendor:publish --tag=activitylog-ui-lang
```

Then edit `lang/vendor/activitylog-ui/{locale}/messages.php`. The UI uses your application
locale automatically, so no further wiring is needed.
