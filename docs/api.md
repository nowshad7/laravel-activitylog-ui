# JSON API

Opt in with `features.api => true`. Two read-only endpoints are exposed under your configured prefix, behind the same middleware, gate and allow-lists as the UI.

## List

```
GET {prefix}/api/activities
```

Accepts the **same filters as the UI** as query parameters (`search`, `log_name`, `model`, `subject_id`, `event`, `causer_type`, `causer_id`, `date_from`, `date_to`, `batch_uuid`, `per_page`).

```json
{
  "data": [
    {
      "id": 42,
      "log_name": "default",
      "description": "updated",
      "event": "updated",
      "subject_type": "App\\Models\\Post",
      "subject_id": 7,
      "causer_type": "App\\Models\\User",
      "causer_id": 1,
      "causer": "Jane Admin",
      "batch_uuid": null,
      "created_at": "2026-10-08T12:00:00+00:00"
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1 },
  "links": { "next": null, "prev": null }
}
```

## Show

```
GET {prefix}/api/activities/{id}
```

Returns one activity plus its computed `changes` diff and `custom_properties`.

## Why

The API makes the activity log composable — consume it from a SPA, an admin dashboard, a reporting job, or an automated agent, reusing the exact filtering the UI offers.
