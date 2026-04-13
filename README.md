# Digital Notice Board

## Features

- Users can create and join notice boards
- Public and private boards (invite-only)
- Poster (image) and flyer (text) submissions
- Admin moderation system:
  - Global admins manage all boards
  - Board admins manage their own board
- Automatic content flagging (keyword-based)
- Automatic expiry (posts removed after 1 week)
- Recommendation system (category-based)
- Notifications for submission approval/rejection

## Testing

All core features are tested using Laravel Feature Tests:

- Board access control (private/public)
- Admin and board-admin permissions
- Submission moderation
- Expiry system
- Authentication and profile management

Run tests with:

```bash
sail artisan test