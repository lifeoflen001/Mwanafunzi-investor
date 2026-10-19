# Complete registered route inventory — 17 October 2026

Generated from `php artisan route:list --json` in the local XAMPP checkout. “Source integrity” means the route loaded and controller-action integrity is covered by `RouteInventoryTest`; “HTTP smoke” is limited to the explicit GET/HEAD routes listed as PASS. Mutating routes require authenticated workflow tests or manual operation testing and are marked accordingly rather than being claimed as fully exercised.

| # | Method | URI | Name | Action | Source integrity | HTTP smoke |
|---:|---|---|---|---|---|---|
| 1 | GET\|HEAD | `/` | `home` | `App\Http\Controllers\PublicSiteController@home` | PASS | PASS |
| 2 | GET\|HEAD | `about` | `about` | `App\Http\Controllers\PublicSiteController@page` | PASS | PASS |
| 3 | GET\|HEAD | `about/team/{member}` | `team.show` | `App\Http\Controllers\PublicSiteController@teamMember` | PASS | NOT DIRECTLY TESTED |
| 4 | GET\|HEAD | `account` | `account.dashboard` | `App\Http\Controllers\CustomerAccountController@dashboard` | PASS | NOT DIRECTLY TESTED |
| 5 | GET\|HEAD | `account/courses` | `account.courses` | `App\Http\Controllers\CustomerAccountController@courses` | PASS | NOT DIRECTLY TESTED |
| 6 | GET\|HEAD | `account/courses/{enrollment}` | `account.courses.show` | `App\Http\Controllers\CustomerAccountController@course` | PASS | NOT DIRECTLY TESTED |
| 7 | GET\|HEAD | `account/courses/{enrollment}/lessons/{lesson}` | `account.courses.lessons.show` | `App\Http\Controllers\CustomerAccountController@lesson` | PASS | NOT DIRECTLY TESTED |
| 8 | POST | `account/courses/{enrollment}/lessons/{lesson}/complete` | `account.courses.lessons.complete` | `App\Http\Controllers\CustomerAccountController@completeLesson` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 9 | GET\|HEAD | `account/downloads` | `account.downloads` | `App\Http\Controllers\CustomerAccountController@downloads` | PASS | NOT DIRECTLY TESTED |
| 10 | GET\|HEAD | `account/orders` | `account.orders` | `App\Http\Controllers\CustomerAccountController@orders` | PASS | NOT DIRECTLY TESTED |
| 11 | GET\|HEAD | `account/orders/{order}` | `account.orders.show` | `App\Http\Controllers\CustomerAccountController@order` | PASS | NOT DIRECTLY TESTED |
| 12 | GET\|HEAD | `account/orders/{order}/receipt` | `account.orders.receipt` | `App\Http\Controllers\CustomerAccountController@receipt` | PASS | NOT DIRECTLY TESTED |
| 13 | GET\|HEAD | `account/profile` | `account.profile` | `App\Http\Controllers\CustomerAccountController@profile` | PASS | NOT DIRECTLY TESTED |
| 14 | PUT | `account/profile` | `account.profile.update` | `App\Http\Controllers\CustomerAccountController@updateProfile` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 15 | POST | `account/profile/avatar` | `account.profile.avatar.update` | `App\Http\Controllers\CustomerAccountController@updateAvatar` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 16 | DELETE | `account/profile/avatar` | `account.profile.avatar.remove` | `App\Http\Controllers\CustomerAccountController@removeAvatar` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 17 | GET\|HEAD | `account/security` | `account.security` | `App\Http\Controllers\CustomerAccountController@security` | PASS | NOT DIRECTLY TESTED |
| 18 | POST | `account/security/google/disconnect` | `account.security.google.disconnect` | `App\Http\Controllers\GoogleAuthController@disconnect` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 19 | GET\|HEAD | `account/security/google/redirect` | `account.security.google.redirect` | `App\Http\Controllers\GoogleAuthController@linkRedirect` | PASS | NOT DIRECTLY TESTED |
| 20 | PUT | `account/security/password` | `account.security.password` | `App\Http\Controllers\CustomerAccountController@updatePassword` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 21 | GET\|HEAD | `account/studio/inquiries` | `account.studio.inquiries` | `App\Http\Controllers\CustomerAccountController@studioInquiries` | PASS | NOT DIRECTLY TESTED |
| 22 | GET\|HEAD | `account/studio/inquiries/{inquiry}` | `account.studio.inquiries.show` | `App\Http\Controllers\CustomerAccountController@studioInquiry` | PASS | NOT DIRECTLY TESTED |
| 23 | GET\|HEAD | `admin` | `admin.dashboard` | `App\Http\Controllers\AdminController@dashboard` | PASS | NOT DIRECTLY TESTED |
| 24 | GET\|HEAD | `admin/administrators` | `admin.administrators` | `App\Http\Controllers\AdminUserController@index` | PASS | NOT DIRECTLY TESTED |
| 25 | POST | `admin/administrators` | `admin.administrators.store` | `App\Http\Controllers\AdminUserController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 26 | GET\|HEAD | `admin/administrators/create` | `admin.administrators.create` | `App\Http\Controllers\AdminUserController@create` | PASS | NOT DIRECTLY TESTED |
| 27 | PUT | `admin/administrators/{administrator}` | `admin.administrators.update` | `App\Http\Controllers\AdminUserController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 28 | GET\|HEAD | `admin/administrators/{administrator}/edit` | `admin.administrators.edit` | `App\Http\Controllers\AdminUserController@edit` | PASS | NOT DIRECTLY TESTED |
| 29 | GET\|HEAD | `admin/articles` | `admin.articles` | `App\Http\Controllers\AdminController@articles` | PASS | NOT DIRECTLY TESTED |
| 30 | POST | `admin/articles` | `admin.articles.store` | `App\Http\Controllers\AdminController@articleStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 31 | GET\|HEAD | `admin/articles/create` | `admin.articles.create` | `App\Http\Controllers\AdminController@articleCreate` | PASS | NOT DIRECTLY TESTED |
| 32 | PUT | `admin/articles/{article}` | `admin.articles.update` | `App\Http\Controllers\AdminController@articleUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 33 | DELETE | `admin/articles/{article}` | `admin.articles.destroy` | `App\Http\Controllers\AdminController@articleDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 34 | GET\|HEAD | `admin/articles/{article}/edit` | `admin.articles.edit` | `App\Http\Controllers\AdminController@articleEdit` | PASS | NOT DIRECTLY TESTED |
| 35 | POST | `admin/articles/{article}/restore` | `admin.articles.restore` | `App\Http\Controllers\AdminController@articleRestore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 36 | GET\|HEAD | `admin/audit` | `admin.audit` | `App\Http\Controllers\AdminAuditController@index` | PASS | NOT DIRECTLY TESTED |
| 37 | GET\|HEAD | `admin/business-units` | `admin.business-units` | `App\Http\Controllers\AdminBusinessUnitController@index` | PASS | NOT DIRECTLY TESTED |
| 38 | PUT | `admin/business-units/{businessUnit}` | `admin.business-units.update` | `App\Http\Controllers\AdminBusinessUnitController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 39 | GET\|HEAD | `admin/categories` | `admin.categories` | `App\Http\Controllers\AdminTaxonomyController@categories` | PASS | NOT DIRECTLY TESTED |
| 40 | POST | `admin/categories` | `admin.categories.store` | `App\Http\Controllers\AdminTaxonomyController@categoryStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 41 | GET\|HEAD | `admin/categories/create` | `admin.categories.create` | `App\Http\Controllers\AdminTaxonomyController@categoryCreate` | PASS | NOT DIRECTLY TESTED |
| 42 | PUT | `admin/categories/{category}` | `admin.categories.update` | `App\Http\Controllers\AdminTaxonomyController@categoryUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 43 | DELETE | `admin/categories/{category}` | `admin.categories.destroy` | `App\Http\Controllers\AdminTaxonomyController@categoryDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 44 | GET\|HEAD | `admin/categories/{category}/edit` | `admin.categories.edit` | `App\Http\Controllers\AdminTaxonomyController@categoryEdit` | PASS | NOT DIRECTLY TESTED |
| 45 | GET\|HEAD | `admin/comments` | `admin.comments` | `App\Http\Controllers\AdminCommentController@index` | PASS | NOT DIRECTLY TESTED |
| 46 | GET\|HEAD | `admin/comments/{comment}` | `admin.comments.show` | `App\Http\Controllers\AdminCommentController@show` | PASS | NOT DIRECTLY TESTED |
| 47 | PATCH | `admin/comments/{comment}` | `admin.comments.update` | `App\Http\Controllers\AdminCommentController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 48 | DELETE | `admin/comments/{comment}` | `admin.comments.destroy` | `App\Http\Controllers\AdminCommentController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 49 | GET\|HEAD | `admin/commerce` | `admin.commerce.dashboard` | `App\Http\Controllers\AdminCommerceController@dashboard` | PASS | NOT DIRECTLY TESTED |
| 50 | GET\|HEAD | `admin/commerce/enrollments` | `admin.commerce.enrollments` | `App\Http\Controllers\AdminCommerceController@enrollments` | PASS | NOT DIRECTLY TESTED |
| 51 | GET\|HEAD | `admin/commerce/entitlements` | `admin.commerce.entitlements` | `App\Http\Controllers\AdminCommerceController@entitlements` | PASS | NOT DIRECTLY TESTED |
| 52 | GET\|HEAD | `admin/commerce/orders` | `admin.commerce.orders` | `App\Http\Controllers\AdminCommerceController@orders` | PASS | NOT DIRECTLY TESTED |
| 53 | GET\|HEAD | `admin/commerce/orders/{order}` | `admin.commerce.orders.show` | `App\Http\Controllers\AdminCommerceController@order` | PASS | NOT DIRECTLY TESTED |
| 54 | POST | `admin/commerce/orders/{order}/refund` | `admin.commerce.orders.refund` | `App\Http\Controllers\AdminCommerceController@refund` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 55 | GET\|HEAD | `admin/commerce/payments` | `admin.commerce.payments` | `App\Http\Controllers\AdminCommerceController@payments` | PASS | NOT DIRECTLY TESTED |
| 56 | GET\|HEAD | `admin/commerce/waitlists` | `admin.commerce.waitlists` | `App\Http\Controllers\AdminCommerceController@waitlists` | PASS | NOT DIRECTLY TESTED |
| 57 | GET\|HEAD | `admin/commerce/waitlists/export` | `admin.commerce.waitlists.export` | `App\Http\Controllers\AdminCommerceController@waitlistExport` | PASS | NOT DIRECTLY TESTED |
| 58 | GET\|HEAD | `admin/compliance` | `admin.compliance` | `App\Http\Controllers\AdminComplianceController@index` | PASS | NOT DIRECTLY TESTED |
| 59 | DELETE | `admin/course-faqs/{faq}` | `admin.course-faqs.destroy` | `App\Http\Controllers\AdminController@courseFaqDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 60 | GET\|HEAD | `admin/courses` | `admin.courses` | `App\Http\Controllers\AdminController@courses` | PASS | NOT DIRECTLY TESTED |
| 61 | POST | `admin/courses` | `admin.courses.store` | `App\Http\Controllers\AdminController@courseStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 62 | GET\|HEAD | `admin/courses/create` | `admin.courses.create` | `App\Http\Controllers\AdminController@courseCreate` | PASS | NOT DIRECTLY TESTED |
| 63 | PUT | `admin/courses/{course}` | `admin.courses.update` | `App\Http\Controllers\AdminController@courseUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 64 | DELETE | `admin/courses/{course}` | `admin.courses.destroy` | `App\Http\Controllers\AdminController@courseDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 65 | GET\|HEAD | `admin/courses/{course}/edit` | `admin.courses.edit` | `App\Http\Controllers\AdminController@courseEdit` | PASS | NOT DIRECTLY TESTED |
| 66 | POST | `admin/courses/{course}/faqs` | `admin.courses.faqs.store` | `App\Http\Controllers\AdminController@courseFaqStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 67 | POST | `admin/courses/{course}/modules` | `admin.courses.modules.store` | `App\Http\Controllers\AdminController@moduleStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 68 | POST | `admin/courses/{course}/restore` | `admin.courses.restore` | `App\Http\Controllers\AdminController@courseRestore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 69 | GET\|HEAD | `admin/customers` | `admin.customers` | `App\Http\Controllers\AdminCustomerController@index` | PASS | NOT DIRECTLY TESTED |
| 70 | POST | `admin/customers` | `admin.customers.store` | `App\Http\Controllers\AdminCustomerController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 71 | GET\|HEAD | `admin/customers/create` | `admin.customers.create` | `App\Http\Controllers\AdminCustomerController@create` | PASS | NOT DIRECTLY TESTED |
| 72 | GET\|HEAD | `admin/customers/{customer}` | `admin.customers.show` | `App\Http\Controllers\AdminCustomerController@show` | PASS | NOT DIRECTLY TESTED |
| 73 | PUT | `admin/customers/{customer}` | `admin.customers.update` | `App\Http\Controllers\AdminCustomerController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 74 | DELETE | `admin/customers/{customer}` | `admin.customers.destroy` | `App\Http\Controllers\AdminCustomerController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 75 | GET\|HEAD | `admin/customers/{customer}/edit` | `admin.customers.edit` | `App\Http\Controllers\AdminCustomerController@edit` | PASS | NOT DIRECTLY TESTED |
| 76 | PATCH | `admin/customers/{customer}/status` | `admin.customers.status` | `App\Http\Controllers\AdminCustomerController@updateStatus` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 77 | GET\|HEAD | `admin/faqs` | `admin.faqs` | `App\Http\Controllers\AdminFaqController@index` | PASS | NOT DIRECTLY TESTED |
| 78 | POST | `admin/faqs` | `admin.faqs.store` | `App\Http\Controllers\AdminFaqController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 79 | DELETE | `admin/faqs/{faq}` | `admin.faqs.destroy` | `App\Http\Controllers\AdminController@productFaqDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 80 | PUT | `admin/faqs/{source}/{id}` | `admin.faqs.update` | `App\Http\Controllers\AdminFaqController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 81 | DELETE | `admin/faqs/{source}/{id}` | `admin.faqs.manage.destroy` | `App\Http\Controllers\AdminFaqController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 82 | DELETE | `admin/features/{feature}` | `admin.features.destroy` | `App\Http\Controllers\AdminController@featureDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 83 | GET\|HEAD | `admin/forgot-password` | `admin.password.request` | `App\Http\Controllers\AdminAuthController@forgotPassword` | PASS | NOT DIRECTLY TESTED |
| 84 | POST | `admin/forgot-password` | `admin.password.email` | `App\Http\Controllers\AdminAuthController@sendPasswordReset` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 85 | GET\|HEAD | `admin/journal` | `admin.journal` | `App\Http\Controllers\AdminController@articles` | PASS | NOT DIRECTLY TESTED |
| 86 | POST | `admin/journal` | `admin.journal.store` | `App\Http\Controllers\AdminController@articleStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 87 | GET\|HEAD | `admin/journal/create` | `admin.journal.create` | `App\Http\Controllers\AdminController@articleCreate` | PASS | NOT DIRECTLY TESTED |
| 88 | POST | `admin/journal/daily-updates` | `admin.journal.daily-updates.store` | `App\Http\Controllers\AdminController@dailyUpdateStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 89 | GET\|HEAD | `admin/journal/daily-updates/create` | `admin.journal.daily-updates.create` | `App\Http\Controllers\AdminController@dailyUpdateCreate` | PASS | NOT DIRECTLY TESTED |
| 90 | PUT | `admin/lessons/{lesson}` | `admin.lessons.update` | `App\Http\Controllers\AdminController@lessonUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 91 | DELETE | `admin/lessons/{lesson}` | `admin.lessons.destroy` | `App\Http\Controllers\AdminController@lessonDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 92 | POST | `admin/lessons/{lesson}/restore` | `admin.lessons.restore` | `App\Http\Controllers\AdminController@lessonRestore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 93 | GET\|HEAD | `admin/login` | `admin.login` | `App\Http\Controllers\AdminAuthController@create` | PASS | PASS |
| 94 | POST | `admin/login` | `admin.login.store` | `App\Http\Controllers\AdminAuthController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 95 | POST | `admin/logout` | `admin.logout` | `App\Http\Controllers\AdminAuthController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 96 | GET\|HEAD | `admin/media` | `admin.media` | `App\Http\Controllers\MediaController@index` | PASS | NOT DIRECTLY TESTED |
| 97 | POST | `admin/media` | `admin.media.store` | `App\Http\Controllers\MediaController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 98 | POST | `admin/media/inline` | `admin.media.inline.store` | `App\Http\Controllers\MediaController@inlineStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 99 | GET\|HEAD | `admin/media/search` | `admin.media.search` | `App\Http\Controllers\MediaController@search` | PASS | NOT DIRECTLY TESTED |
| 100 | PUT | `admin/media/{media}` | `admin.media.update` | `App\Http\Controllers\MediaController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 101 | DELETE | `admin/media/{media}` | `admin.media.destroy` | `App\Http\Controllers\MediaController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 102 | GET\|HEAD | `admin/messages` | `admin.messages` | `App\Http\Controllers\AdminController@messages` | PASS | NOT DIRECTLY TESTED |
| 103 | GET\|HEAD | `admin/messages/{message}` | `admin.messages.show` | `App\Http\Controllers\AdminController@messageShow` | PASS | NOT DIRECTLY TESTED |
| 104 | PATCH | `admin/messages/{message}` | `admin.messages.update` | `App\Http\Controllers\AdminController@messageUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 105 | POST | `admin/messages/{message}/read` | `admin.messages.read` | `App\Http\Controllers\AdminController@messageRead` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 106 | GET\|HEAD | `admin/mfa/challenge` | `admin.mfa.challenge` | `App\Http\Controllers\AdminMfaController@challenge` | PASS | NOT DIRECTLY TESTED |
| 107 | POST | `admin/mfa/challenge` | `admin.mfa.challenge.verify` | `App\Http\Controllers\AdminMfaController@verifyChallenge` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 108 | GET\|HEAD | `admin/mfa/setup` | `admin.mfa.setup` | `App\Http\Controllers\AdminMfaController@setup` | PASS | NOT DIRECTLY TESTED |
| 109 | POST | `admin/mfa/setup` | `admin.mfa.setup.store` | `App\Http\Controllers\AdminMfaController@storeSetup` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 110 | PUT | `admin/modules/{module}` | `admin.modules.update` | `App\Http\Controllers\AdminController@moduleUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 111 | DELETE | `admin/modules/{module}` | `admin.modules.destroy` | `App\Http\Controllers\AdminController@moduleDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 112 | POST | `admin/modules/{module}/lessons` | `admin.modules.lessons.store` | `App\Http\Controllers\AdminController@lessonStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 113 | POST | `admin/modules/{module}/restore` | `admin.modules.restore` | `App\Http\Controllers\AdminController@moduleRestore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 114 | GET\|HEAD | `admin/navigation` | `admin.navigation` | `App\Http\Controllers\AdminNavigationController@index` | PASS | NOT DIRECTLY TESTED |
| 115 | POST | `admin/navigation` | `admin.navigation.store` | `App\Http\Controllers\AdminNavigationController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 116 | PUT | `admin/navigation/{navigationItem}` | `admin.navigation.update` | `App\Http\Controllers\AdminNavigationController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 117 | DELETE | `admin/navigation/{navigationItem}` | `admin.navigation.destroy` | `App\Http\Controllers\AdminNavigationController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 118 | GET\|HEAD | `admin/newsletter` | `admin.newsletter` | `App\Http\Controllers\AdminNewsletterController@index` | PASS | NOT DIRECTLY TESTED |
| 119 | POST | `admin/newsletter` | `admin.newsletter.store` | `App\Http\Controllers\AdminNewsletterController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 120 | GET\|HEAD | `admin/newsletter/export` | `admin.newsletter.export` | `App\Http\Controllers\AdminNewsletterController@export` | PASS | NOT DIRECTLY TESTED |
| 121 | GET\|HEAD | `admin/newsletter/{subscriber}` | `admin.newsletter.show` | `App\Http\Controllers\AdminNewsletterController@show` | PASS | NOT DIRECTLY TESTED |
| 122 | DELETE | `admin/newsletter/{subscriber}` | `admin.newsletter.destroy` | `App\Http\Controllers\AdminNewsletterController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 123 | POST | `admin/newsletter/{subscriber}/resend` | `admin.newsletter.resend` | `App\Http\Controllers\AdminNewsletterController@resend` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 124 | POST | `admin/newsletter/{subscriber}/restore` | `admin.newsletter.restore` | `App\Http\Controllers\AdminNewsletterController@restore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 125 | POST | `admin/newsletter/{subscriber}/unsubscribe` | `admin.newsletter.unsubscribe.admin` | `App\Http\Controllers\AdminNewsletterController@unsubscribe` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 126 | GET\|HEAD | `admin/notifications` | `admin.notifications` | `App\Http\Controllers\AdminNotificationController@index` | PASS | NOT DIRECTLY TESTED |
| 127 | POST | `admin/notifications/read-all` | `admin.notifications.read-all` | `App\Http\Controllers\AdminNotificationController@readAll` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 128 | POST | `admin/notifications/{message}/read` | `admin.notifications.read` | `App\Http\Controllers\AdminNotificationController@read` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 129 | GET\|HEAD | `admin/pages` | `admin.pages` | `App\Http\Controllers\AdminPageController@index` | PASS | NOT DIRECTLY TESTED |
| 130 | POST | `admin/pages` | `admin.pages.store` | `App\Http\Controllers\AdminPageController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 131 | GET\|HEAD | `admin/pages/create` | `admin.pages.create` | `App\Http\Controllers\AdminPageController@create` | PASS | NOT DIRECTLY TESTED |
| 132 | PUT | `admin/pages/{page}` | `admin.pages.update` | `App\Http\Controllers\AdminPageController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 133 | GET\|HEAD | `admin/pages/{page}/edit` | `admin.pages.edit` | `App\Http\Controllers\AdminPageController@edit` | PASS | NOT DIRECTLY TESTED |
| 134 | GET\|HEAD | `admin/policies` | `admin.policies` | `App\Http\Controllers\AdminPageController@policies` | PASS | NOT DIRECTLY TESTED |
| 135 | GET\|HEAD | `admin/preview/{type}/{id}` | `admin.preview` | `App\Http\Controllers\PublicSiteController@preview` | PASS | NOT DIRECTLY TESTED |
| 136 | DELETE | `admin/product-assets/{asset}` | `admin.product-assets.destroy` | `App\Http\Controllers\AdminCommerceController@assetDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 137 | DELETE | `admin/product-images/{image}` | `admin.product-images.destroy` | `App\Http\Controllers\AdminController@productImageDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 138 | GET\|HEAD | `admin/products` | `admin.products` | `App\Http\Controllers\AdminController@products` | PASS | NOT DIRECTLY TESTED |
| 139 | POST | `admin/products` | `admin.products.store` | `App\Http\Controllers\AdminController@productStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 140 | GET\|HEAD | `admin/products/create` | `admin.products.create` | `App\Http\Controllers\AdminController@productCreate` | PASS | NOT DIRECTLY TESTED |
| 141 | PUT | `admin/products/{product}` | `admin.products.update` | `App\Http\Controllers\AdminController@productUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 142 | DELETE | `admin/products/{product}` | `admin.products.destroy` | `App\Http\Controllers\AdminController@productDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 143 | POST | `admin/products/{product}/assets` | `admin.products.assets.store` | `App\Http\Controllers\AdminCommerceController@assetStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 144 | GET\|HEAD | `admin/products/{product}/edit` | `admin.products.edit` | `App\Http\Controllers\AdminController@productEdit` | PASS | NOT DIRECTLY TESTED |
| 145 | POST | `admin/products/{product}/faqs` | `admin.products.faqs.store` | `App\Http\Controllers\AdminController@productFaqStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 146 | POST | `admin/products/{product}/features` | `admin.products.features.store` | `App\Http\Controllers\AdminController@featureStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 147 | POST | `admin/products/{product}/images` | `admin.products.images.store` | `App\Http\Controllers\AdminController@productImageStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 148 | POST | `admin/products/{product}/restore` | `admin.products.restore` | `App\Http\Controllers\AdminController@productRestore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 149 | POST | `admin/products/{product}/versions` | `admin.products.versions.store` | `App\Http\Controllers\AdminController@versionStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 150 | GET\|HEAD | `admin/profile` | `admin.profile` | `App\Http\Controllers\AdminProfileController@show` | PASS | NOT DIRECTLY TESTED |
| 151 | PUT | `admin/profile` | `admin.profile.update` | `App\Http\Controllers\AdminProfileController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 152 | POST | `admin/profile/avatar` | `admin.profile.avatar.update` | `App\Http\Controllers\AdminProfileController@updateAvatar` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 153 | DELETE | `admin/profile/avatar` | `admin.profile.avatar.remove` | `App\Http\Controllers\AdminProfileController@removeAvatar` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 154 | POST | `admin/profile/mfa/disable` | `admin.profile.mfa.disable` | `App\Http\Controllers\AdminMfaController@disable` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 155 | PUT | `admin/profile/password` | `admin.profile.password` | `App\Http\Controllers\AdminProfileController@updatePassword` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 156 | GET\|HEAD | `admin/projects` | `admin.projects` | `App\Http\Controllers\AdminProjectController@index` | PASS | NOT DIRECTLY TESTED |
| 157 | POST | `admin/projects` | `admin.projects.store` | `App\Http\Controllers\AdminProjectController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 158 | GET\|HEAD | `admin/projects/create` | `admin.projects.create` | `App\Http\Controllers\AdminProjectController@create` | PASS | NOT DIRECTLY TESTED |
| 159 | PUT | `admin/projects/{project}` | `admin.projects.update` | `App\Http\Controllers\AdminProjectController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 160 | DELETE | `admin/projects/{project}` | `admin.projects.destroy` | `App\Http\Controllers\AdminProjectController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 161 | GET\|HEAD | `admin/projects/{project}/edit` | `admin.projects.edit` | `App\Http\Controllers\AdminProjectController@edit` | PASS | NOT DIRECTLY TESTED |
| 162 | GET\|HEAD | `admin/redirects` | `admin.redirects` | `App\Http\Controllers\AdminRedirectController@index` | PASS | NOT DIRECTLY TESTED |
| 163 | POST | `admin/redirects` | `admin.redirects.store` | `App\Http\Controllers\AdminRedirectController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 164 | PUT | `admin/redirects/{redirect}` | `admin.redirects.update` | `App\Http\Controllers\AdminRedirectController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 165 | DELETE | `admin/redirects/{redirect}` | `admin.redirects.destroy` | `App\Http\Controllers\AdminRedirectController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 166 | POST | `admin/reset-password` | `admin.password.update` | `App\Http\Controllers\AdminAuthController@updatePasswordFromReset` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 167 | GET\|HEAD | `admin/reset-password/{token}` | `admin.password.reset` | `App\Http\Controllers\AdminAuthController@resetPassword` | PASS | NOT DIRECTLY TESTED |
| 168 | GET\|HEAD | `admin/search` | `admin.search` | `App\Http\Controllers\AdminSearchController@index` | PASS | NOT DIRECTLY TESTED |
| 169 | GET\|HEAD | `admin/search/suggestions` | `admin.search.suggestions` | `App\Http\Controllers\AdminSearchController@suggestions` | PASS | NOT DIRECTLY TESTED |
| 170 | GET\|HEAD | `admin/seo` | `admin.seo` | `App\Http\Controllers\AdminSeoController@index` | PASS | NOT DIRECTLY TESTED |
| 171 | GET\|HEAD | `admin/services` | `admin.services` | `App\Http\Controllers\AdminServiceController@index` | PASS | NOT DIRECTLY TESTED |
| 172 | POST | `admin/services` | `admin.services.store` | `App\Http\Controllers\AdminServiceController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 173 | GET\|HEAD | `admin/services/create` | `admin.services.create` | `App\Http\Controllers\AdminServiceController@create` | PASS | NOT DIRECTLY TESTED |
| 174 | PUT | `admin/services/{service}` | `admin.services.update` | `App\Http\Controllers\AdminServiceController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 175 | DELETE | `admin/services/{service}` | `admin.services.destroy` | `App\Http\Controllers\AdminServiceController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 176 | GET\|HEAD | `admin/services/{service}/edit` | `admin.services.edit` | `App\Http\Controllers\AdminServiceController@edit` | PASS | NOT DIRECTLY TESTED |
| 177 | GET\|HEAD | `admin/settings` | `admin.settings` | `App\Http\Controllers\SiteSettingsController@edit` | PASS | NOT DIRECTLY TESTED |
| 178 | PUT | `admin/settings` | `admin.settings.update` | `App\Http\Controllers\SiteSettingsController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 179 | GET\|HEAD | `admin/settings/cookies` | `admin.settings.cookies` | `App\Http\Controllers\AdminPrivacyController@settings` | PASS | NOT DIRECTLY TESTED |
| 180 | PUT | `admin/settings/cookies` | `admin.settings.cookies.update` | `App\Http\Controllers\AdminPrivacyController@updateSettings` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 181 | GET\|HEAD | `admin/settings/cookies/registry` | `admin.settings.cookies.registry` | `App\Http\Controllers\AdminPrivacyController@registry` | PASS | NOT DIRECTLY TESTED |
| 182 | POST | `admin/settings/cookies/registry` | `admin.settings.cookies.registry.store` | `App\Http\Controllers\AdminPrivacyController@storeRegistry` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 183 | PUT | `admin/settings/cookies/registry/{entry}` | `admin.settings.cookies.registry.update` | `App\Http\Controllers\AdminPrivacyController@updateRegistry` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 184 | DELETE | `admin/settings/cookies/registry/{entry}` | `admin.settings.cookies.registry.destroy` | `App\Http\Controllers\AdminPrivacyController@destroyRegistry` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 185 | GET\|HEAD | `admin/settings/newsletter` | `admin.settings.newsletter` | `App\Http\Controllers\AdminNewsletterController@settings` | PASS | NOT DIRECTLY TESTED |
| 186 | PUT | `admin/settings/newsletter` | `admin.settings.newsletter.update` | `App\Http\Controllers\AdminNewsletterController@updateSettings` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 187 | GET\|HEAD | `admin/settings/security` | `admin.settings.security` | `App\Http\Controllers\SiteSettingsController@security` | PASS | NOT DIRECTLY TESTED |
| 188 | PUT | `admin/settings/security` | `admin.settings.security.update` | `App\Http\Controllers\SiteSettingsController@updateSecurity` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 189 | GET\|HEAD | `admin/social-links` | `admin.social-links` | `App\Http\Controllers\AdminTaxonomyController@socialLinks` | PASS | NOT DIRECTLY TESTED |
| 190 | POST | `admin/social-links` | `admin.social-links.store` | `App\Http\Controllers\AdminTaxonomyController@socialLinkStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 191 | PUT | `admin/social-links/{socialLink}` | `admin.social-links.update` | `App\Http\Controllers\AdminTaxonomyController@socialLinkUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 192 | DELETE | `admin/social-links/{socialLink}` | `admin.social-links.destroy` | `App\Http\Controllers\AdminTaxonomyController@socialLinkDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 193 | GET\|HEAD | `admin/studio/inquiries` | `admin.studio.inquiries` | `App\Http\Controllers\AdminStudioInquiryController@index` | PASS | NOT DIRECTLY TESTED |
| 194 | GET\|HEAD | `admin/studio/inquiries/{inquiry}` | `admin.studio.inquiries.show` | `App\Http\Controllers\AdminStudioInquiryController@show` | PASS | NOT DIRECTLY TESTED |
| 195 | PATCH | `admin/studio/inquiries/{inquiry}` | `admin.studio.inquiries.update` | `App\Http\Controllers\AdminStudioInquiryController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 196 | GET\|HEAD | `admin/studio/inquiries/{inquiry}/references/{file}` | `admin.studio.inquiries.references.download` | `App\Http\Controllers\AdminStudioInquiryController@downloadReference` | PASS | NOT DIRECTLY TESTED |
| 197 | GET\|HEAD | `admin/tags` | `admin.tags` | `App\Http\Controllers\AdminTaxonomyController@tags` | PASS | NOT DIRECTLY TESTED |
| 198 | POST | `admin/tags` | `admin.tags.store` | `App\Http\Controllers\AdminTaxonomyController@tagStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 199 | GET\|HEAD | `admin/tags/create` | `admin.tags.create` | `App\Http\Controllers\AdminTaxonomyController@tagCreate` | PASS | NOT DIRECTLY TESTED |
| 200 | PUT | `admin/tags/{tag}` | `admin.tags.update` | `App\Http\Controllers\AdminTaxonomyController@tagUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 201 | DELETE | `admin/tags/{tag}` | `admin.tags.destroy` | `App\Http\Controllers\AdminTaxonomyController@tagDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 202 | GET\|HEAD | `admin/tags/{tag}/edit` | `admin.tags.edit` | `App\Http\Controllers\AdminTaxonomyController@tagEdit` | PASS | NOT DIRECTLY TESTED |
| 203 | GET\|HEAD | `admin/team` | `admin.team` | `App\Http\Controllers\AdminTeamMemberController@index` | PASS | NOT DIRECTLY TESTED |
| 204 | POST | `admin/team` | `admin.team.store` | `App\Http\Controllers\AdminTeamMemberController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 205 | GET\|HEAD | `admin/team/create` | `admin.team.create` | `App\Http\Controllers\AdminTeamMemberController@create` | PASS | NOT DIRECTLY TESTED |
| 206 | PUT | `admin/team/{teamMember}` | `admin.team.update` | `App\Http\Controllers\AdminTeamMemberController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 207 | DELETE | `admin/team/{teamMember}` | `admin.team.destroy` | `App\Http\Controllers\AdminTeamMemberController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 208 | GET\|HEAD | `admin/team/{teamMember}/edit` | `admin.team.edit` | `App\Http\Controllers\AdminTeamMemberController@edit` | PASS | NOT DIRECTLY TESTED |
| 209 | GET\|HEAD | `admin/testimonials` | `admin.testimonials` | `App\Http\Controllers\AdminTestimonialController@index` | PASS | NOT DIRECTLY TESTED |
| 210 | POST | `admin/testimonials` | `admin.testimonials.store` | `App\Http\Controllers\AdminTestimonialController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 211 | GET\|HEAD | `admin/testimonials/create` | `admin.testimonials.create` | `App\Http\Controllers\AdminTestimonialController@create` | PASS | NOT DIRECTLY TESTED |
| 212 | PUT | `admin/testimonials/{testimonial}` | `admin.testimonials.update` | `App\Http\Controllers\AdminTestimonialController@update` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 213 | DELETE | `admin/testimonials/{testimonial}` | `admin.testimonials.destroy` | `App\Http\Controllers\AdminTestimonialController@destroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 214 | GET\|HEAD | `admin/testimonials/{testimonial}/edit` | `admin.testimonials.edit` | `App\Http\Controllers\AdminTestimonialController@edit` | PASS | NOT DIRECTLY TESTED |
| 215 | GET\|HEAD | `admin/topics` | `admin.topics` | `App\Http\Controllers\AdminController@topics` | PASS | NOT DIRECTLY TESTED |
| 216 | POST | `admin/topics` | `admin.topics.store` | `App\Http\Controllers\AdminController@topicStore` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 217 | GET\|HEAD | `admin/topics/create` | `admin.topics.create` | `App\Http\Controllers\AdminController@topicCreate` | PASS | NOT DIRECTLY TESTED |
| 218 | PUT | `admin/topics/{topic}` | `admin.topics.update` | `App\Http\Controllers\AdminController@topicUpdate` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 219 | GET\|HEAD | `admin/topics/{topic}/edit` | `admin.topics.edit` | `App\Http\Controllers\AdminController@topicEdit` | PASS | NOT DIRECTLY TESTED |
| 220 | DELETE | `admin/versions/{version}` | `admin.versions.destroy` | `App\Http\Controllers\AdminController@versionDestroy` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 221 | GET\|HEAD | `auth/google/callback` | `auth.google.callback` | `App\Http\Controllers\GoogleAuthController@callback` | PASS | NOT DIRECTLY TESTED |
| 222 | GET\|HEAD | `auth/google/redirect` | `auth.google.redirect` | `App\Http\Controllers\GoogleAuthController@redirect` | PASS | NOT DIRECTLY TESTED |
| 223 | GET\|HEAD | `blog` | `blog` | `App\Http\Controllers\PublicSiteController@journal` | PASS | NOT DIRECTLY TESTED |
| 224 | GET\|HEAD | `blog/author/{member}` | `blog.author` | `App\Http\Controllers\PublicSiteController@journalAuthor` | PASS | NOT DIRECTLY TESTED |
| 225 | GET\|HEAD | `blog/category/{category}` | `blog.category` | `App\Http\Controllers\PublicSiteController@journalCategory` | PASS | NOT DIRECTLY TESTED |
| 226 | GET\|HEAD | `blog/tag/{tag}` | `blog.tag` | `App\Http\Controllers\PublicSiteController@journalTag` | PASS | NOT DIRECTLY TESTED |
| 227 | GET\|HEAD | `blog/{article}` | `blog.show` | `App\Http\Controllers\PublicSiteController@article` | PASS | NOT DIRECTLY TESTED |
| 228 | POST | `blog/{article}/comments` | `blog.comments.store` | `App\Http\Controllers\EditorialInteractionController@comment` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 229 | POST | `blog/{article}/reaction` | `blog.reaction` | `App\Http\Controllers\EditorialInteractionController@react` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 230 | GET\|HEAD | `checkout/course/{course}` | `checkout.course` | `App\Http\Controllers\CommerceController@courseCheckout` | PASS | NOT DIRECTLY TESTED |
| 231 | POST | `checkout/course/{course}` | `checkout.course.store` | `App\Http\Controllers\CommerceController@storeCourse` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 232 | GET\|HEAD | `checkout/product/{product}` | `checkout.product` | `App\Http\Controllers\CommerceController@productCheckout` | PASS | NOT DIRECTLY TESTED |
| 233 | POST | `checkout/product/{product}` | `checkout.product.store` | `App\Http\Controllers\CommerceController@storeProduct` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 234 | POST | `comments/{comment}/report` | `comments.report` | `App\Http\Controllers\EditorialInteractionController@report` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 235 | GET\|HEAD | `contact` | `contact` | `App\Http\Controllers\PublicSiteController@contact` | PASS | PASS |
| 236 | POST | `contact` | `contact.submit` | `App\Http\Controllers\PublicSiteController@submitContact` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 237 | GET\|HEAD | `cookie-policy` | `cookie-policy` | `App\Http\Controllers\PublicSiteController@cookiePolicy` | PASS | PASS |
| 238 | GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS | `courses` | `courses.legacy` | `Illuminate\Routing\RedirectController` | PASS | NOT DIRECTLY TESTED |
| 239 | GET\|HEAD | `courses/{course}` | `courses.legacy.show` | `Closure` | PASS | NOT DIRECTLY TESTED |
| 240 | POST | `courses/{course}/waitlist` | `courses.waitlist.legacy` | `App\Http\Controllers\WaitlistController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 241 | GET\|HEAD | `creative-studio` | `creative-studio` | `App\Http\Controllers\PublicSiteController@module` | PASS | PASS |
| 242 | GET\|HEAD | `creative-studio/book` | `creative-studio.book` | `App\Http\Controllers\StudioInquiryController@create` | PASS | NOT DIRECTLY TESTED |
| 243 | POST | `creative-studio/book` | `creative-studio.book.store` | `App\Http\Controllers\StudioInquiryController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 244 | GET\|HEAD | `creative-studio/projects` | `creative-studio.projects` | `App\Http\Controllers\PublicSiteController@moduleCollection` | PASS | NOT DIRECTLY TESTED |
| 245 | GET\|HEAD | `creative-studio/services` | `creative-studio.services` | `App\Http\Controllers\PublicSiteController@moduleCollection` | PASS | NOT DIRECTLY TESTED |
| 246 | GET\|HEAD | `creative-studio/services/{service}` | `creative-studio.services.show` | `App\Http\Controllers\PublicSiteController@serviceForModule` | PASS | NOT DIRECTLY TESTED |
| 247 | GET\|HEAD | `creative-studio/testimonials` | `creative-studio.testimonials` | `App\Http\Controllers\PublicSiteController@moduleCollection` | PASS | NOT DIRECTLY TESTED |
| 248 | GET\|HEAD | `development` | `development` | `App\Http\Controllers\PublicSiteController@module` | PASS | NOT DIRECTLY TESTED |
| 249 | GET\|HEAD | `digital-software` | `digital-systems` | `App\Http\Controllers\PublicSiteController@module` | PASS | PASS |
| 250 | GET\|HEAD | `digital-software/products` | `digital-software.products` | `App\Http\Controllers\PublicSiteController@moduleCollection` | PASS | NOT DIRECTLY TESTED |
| 251 | GET\|HEAD | `digital-software/projects` | `digital-software.projects` | `App\Http\Controllers\PublicSiteController@moduleCollection` | PASS | NOT DIRECTLY TESTED |
| 252 | GET\|HEAD | `digital-software/testimonials` | `digital-software.testimonials` | `App\Http\Controllers\PublicSiteController@moduleCollection` | PASS | NOT DIRECTLY TESTED |
| 253 | GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS | `digital-systems` | `digital-systems.legacy` | `Illuminate\Routing\RedirectController` | PASS | NOT DIRECTLY TESTED |
| 254 | GET\|HEAD | `downloads/{asset}` | `downloads.asset` | `App\Http\Controllers\DownloadController` | PASS | NOT DIRECTLY TESTED |
| 255 | POST | `email/verification-notification` | `verification.send` | `App\Http\Controllers\CustomerAuthController@resendVerification` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 256 | GET\|HEAD | `email/verify` | `verification.notice` | `App\Http\Controllers\CustomerAuthController@verificationNotice` | PASS | NOT DIRECTLY TESTED |
| 257 | GET\|HEAD | `email/verify/{id}/{hash}` | `verification.verify` | `App\Http\Controllers\CustomerAuthController@verify` | PASS | NOT DIRECTLY TESTED |
| 258 | GET\|HEAD | `feed` | `feed` | `App\Http\Controllers\PublicSiteController@feed` | PASS | NOT DIRECTLY TESTED |
| 259 | GET\|HEAD | `financial-academy` | `forex-academy` | `App\Http\Controllers\PublicSiteController@academy` | PASS | PASS |
| 260 | GET\|HEAD | `financial-academy/courses` | `courses` | `App\Http\Controllers\PublicSiteController@courses` | PASS | NOT DIRECTLY TESTED |
| 261 | GET\|HEAD | `financial-academy/courses/{course}` | `courses.show` | `App\Http\Controllers\PublicSiteController@course` | PASS | NOT DIRECTLY TESTED |
| 262 | POST | `financial-academy/courses/{course}/waitlist` | `courses.waitlist` | `App\Http\Controllers\WaitlistController@store` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 263 | GET\|HEAD | `financial-academy/learn` | `learn` | `App\Http\Controllers\PublicSiteController@learn` | PASS | NOT DIRECTLY TESTED |
| 264 | GET\|HEAD | `financial-academy/learn/{topic}` | `learn.show` | `App\Http\Controllers\PublicSiteController@topic` | PASS | NOT DIRECTLY TESTED |
| 265 | GET\|HEAD | `financial-academy/tools` | `tools` | `App\Http\Controllers\PublicSiteController@tools` | PASS | NOT DIRECTLY TESTED |
| 266 | GET\|HEAD | `financial-academy/tools/{product}` | `tools.show` | `App\Http\Controllers\PublicSiteController@tool` | PASS | NOT DIRECTLY TESTED |
| 267 | GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS | `forex-academy` | `forex-academy.legacy` | `Illuminate\Routing\RedirectController` | PASS | NOT DIRECTLY TESTED |
| 268 | GET\|HEAD | `forgot-password` | `password.request` | `App\Http\Controllers\CustomerAuthController@forgotPassword` | PASS | NOT DIRECTLY TESTED |
| 269 | POST | `forgot-password` | `password.email` | `App\Http\Controllers\CustomerAuthController@sendPasswordReset` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 270 | GET\|HEAD | `health` | `health` | `Closure` | PASS | PASS |
| 271 | GET\|HEAD | `health/live` | `health.live` | `Closure` | PASS | PASS |
| 272 | GET\|HEAD | `health/ready` | `health.ready` | `Closure` | PASS | PASS |
| 273 | GET\|HEAD | `journal` | `journal` | `App\Http\Controllers\PublicSiteController@journal` | PASS | PASS |
| 274 | GET\|HEAD | `journal/author/{member}` | `journal.author` | `App\Http\Controllers\PublicSiteController@journalAuthor` | PASS | NOT DIRECTLY TESTED |
| 275 | GET\|HEAD | `journal/category/{category}` | `journal.category` | `App\Http\Controllers\PublicSiteController@journalCategory` | PASS | NOT DIRECTLY TESTED |
| 276 | GET\|HEAD | `journal/tag/{tag}` | `journal.tag` | `App\Http\Controllers\PublicSiteController@journalTag` | PASS | NOT DIRECTLY TESTED |
| 277 | GET\|HEAD | `journal/{article}` | `journal.show` | `App\Http\Controllers\PublicSiteController@article` | PASS | NOT DIRECTLY TESTED |
| 278 | POST | `journal/{article}/comments` | `journal.comments.store` | `App\Http\Controllers\EditorialInteractionController@comment` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 279 | POST | `journal/{article}/reaction` | `journal.reaction` | `App\Http\Controllers\EditorialInteractionController@react` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 280 | GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS | `learn` | `learn.legacy` | `Illuminate\Routing\RedirectController` | PASS | NOT DIRECTLY TESTED |
| 281 | GET\|HEAD | `learn/{topic}` | `learn.legacy.show` | `Closure` | PASS | NOT DIRECTLY TESTED |
| 282 | GET\|HEAD | `login` | `login` | `App\Http\Controllers\CustomerAuthController@login` | PASS | PASS |
| 283 | POST | `login` | `login.store` | `App\Http\Controllers\CustomerAuthController@storeLogin` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 284 | POST | `logout` | `logout` | `App\Http\Controllers\CustomerAuthController@logout` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 285 | GET\|HEAD | `newsletter/confirm/{token}` | `newsletter.confirm` | `App\Http\Controllers\NewsletterController@confirm` | PASS | NOT DIRECTLY TESTED |
| 286 | POST | `newsletter/subscribe` | `newsletter.subscribe` | `App\Http\Controllers\NewsletterController@subscribe` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 287 | GET\|HEAD | `newsletter/unsubscribe/{token}` | `newsletter.unsubscribe` | `App\Http\Controllers\NewsletterController@unsubscribe` | PASS | NOT DIRECTLY TESTED |
| 288 | GET\|HEAD | `pages/{slug}` | `pages.show` | `App\Http\Controllers\PublicSiteController@pageBySlug` | PASS | NOT DIRECTLY TESTED |
| 289 | GET\|HEAD | `payments/failure/{order}` | `payments.failure` | `App\Http\Controllers\CommerceController@failure` | PASS | NOT DIRECTLY TESTED |
| 290 | GET\|HEAD | `payments/flutterwave/return` | `payments.flutterwave.return` | `App\Http\Controllers\CommerceController@flutterwaveReturn` | PASS | NOT DIRECTLY TESTED |
| 291 | POST | `payments/flutterwave/webhook` | `payments.flutterwave.webhook` | `App\Http\Controllers\CommerceController@flutterwaveWebhook` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 292 | GET\|HEAD | `payments/processing/{order}` | `payments.processing` | `App\Http\Controllers\CommerceController@processing` | PASS | NOT DIRECTLY TESTED |
| 293 | GET\|HEAD | `payments/sandbox/{payment}` | `payments.sandbox` | `App\Http\Controllers\CommerceController@sandbox` | PASS | NOT DIRECTLY TESTED |
| 294 | POST | `payments/sandbox/{payment}` | `payments.sandbox.complete` | `App\Http\Controllers\CommerceController@completeSandbox` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 295 | GET\|HEAD | `payments/success/{order}` | `payments.success` | `App\Http\Controllers\CommerceController@success` | PASS | NOT DIRECTLY TESTED |
| 296 | GET\|HEAD | `projects` | `projects` | `App\Http\Controllers\PublicSiteController@projects` | PASS | NOT DIRECTLY TESTED |
| 297 | GET\|HEAD | `projects/{project}` | `projects.show` | `App\Http\Controllers\PublicSiteController@project` | PASS | NOT DIRECTLY TESTED |
| 298 | GET\|HEAD | `register` | `register` | `App\Http\Controllers\CustomerAuthController@register` | PASS | NOT DIRECTLY TESTED |
| 299 | POST | `register` | `register.store` | `App\Http\Controllers\CustomerAuthController@storeRegister` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 300 | POST | `reset-password` | `password.update` | `App\Http\Controllers\CustomerAuthController@updatePasswordFromReset` | PASS | WORKFLOW TEST / NOT DIRECTLY SMOKED |
| 301 | GET\|HEAD | `reset-password/{token}` | `password.reset` | `App\Http\Controllers\CustomerAuthController@resetPassword` | PASS | NOT DIRECTLY TESTED |
| 302 | GET\|HEAD | `robots.txt` | `robots` | `Closure` | PASS | PASS |
| 303 | GET\|HEAD | `services` | `services` | `App\Http\Controllers\PublicSiteController@services` | PASS | NOT DIRECTLY TESTED |
| 304 | GET\|HEAD | `services/{service}` | `services.show` | `App\Http\Controllers\PublicSiteController@service` | PASS | NOT DIRECTLY TESTED |
| 305 | GET\|HEAD | `site-map` | `site-map` | `App\Http\Controllers\PublicSiteController@htmlSiteMap` | PASS | PASS |
| 306 | GET\|HEAD | `sitemap.xml` | `sitemap` | `App\Http\Controllers\PublicSiteController@sitemap` | PASS | PASS |
| 307 | GET\|HEAD | `student-of-money` | `student-of-money` | `App\Http\Controllers\PublicSiteController@page` | PASS | NOT DIRECTLY TESTED |
| 308 | GET\|HEAD | `studio` | `studio` | `App\Http\Controllers\PublicSiteController@module` | PASS | NOT DIRECTLY TESTED |
| 309 | GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS | `tools` | `tools.legacy` | `Illuminate\Routing\RedirectController` | PASS | NOT DIRECTLY TESTED |
| 310 | GET\|HEAD | `tools/{product}` | `tools.legacy.show` | `Closure` | PASS | NOT DIRECTLY TESTED |
| 311 | GET\|HEAD | `up` | `` | `Closure` | PASS | NOT DIRECTLY TESTED |
| 312 | GET\|HEAD | `{page}` | `legal` | `App\Http\Controllers\PublicSiteController@page` | PASS | NOT DIRECTLY TESTED |
| 313 | GET\|HEAD | `{path}` | `redirect.handle` | `App\Http\Controllers\PublicRedirectController@handle` | PASS | NOT DIRECTLY TESTED |

## Inventory totals

- Total routes: 313
- GET/HEAD-capable routes: 177
- Mutating routes: 136
- Routes under `admin/*`: 197
- Explicit HTTP smoke passes: 16

## Interpretation

This inventory is complete at the routing layer. A “PASS” in the source-integrity column does not replace end-to-end verification of every authenticated, mutating or third-party-backed workflow; those are represented in the main audit report and the automated feature-test suite. Routes not individually HTTP-smoked are intentionally marked so coverage is not overstated.
