# Fix Route [assets.review.show] not defined

## Steps:
- [x] 1. Create app/Http/Controllers/AssetRegisterController.php with show($id, $action) method.
- [x] 2. Edit routes/web.php: Remove duplicate `/asset-view` routes at the end, add controller routes: GET /assets/{id} (assets.view), /assets/{id}/review (assets.review.show), /assets/{id}/approval (assets.approval.show).
- [x] 3. Edit resources/views/livewire/validations/fixed-asset-approval-lists.blade.php: Fix route name from 'aassets.approval.show' to 'assets.approval.show'.
- [x] 4. Clear route cache: php artisan route:clear && php artisan route:list | findstr assets
- [ ] 5. Test links in review/approval lists.
- [ ] 6. Complete task.

Progress: Steps 1-4 completed. Route 'assets.review.show' now defined: assets/{id}/review → AssetRegisterController@show. Ready for testing.
