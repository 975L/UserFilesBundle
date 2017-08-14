# Changelog

v1.6
----
- Add of a template to display if user is not allowed to use the ressource (20/07/2017)
- Move call to Avatar from template `Profile/show.html.twig` instead of `Profile/show_content.html.twig` to align with other (14/08/2017)
- Add of a Twig extension to check if gravatar has to be displayed
- Remove of overriding FOSUserBundle Controllers methods as replaced by Twig extension
- Add of a file to override to add options in navbar menu

v1.5
----
- Add of a template for navbar menu (19/07/2017)
- Re-organisation of templates to have a clearer view

v1.4.1
------
- Add of missing use in Controller for FOSUser actions (19/07/2017)

v1.4
----
- Correction in Route `userfiles_dashboard` (10/07/2017)
- Correction of Gravatar error in `userfiles_delete_account` Route
- Update of README.md to include information about c975L/EmailBundle (15/07/2017)
- Add databaseEmail as config value to save or not the email sent to deleted user in database linked to c975L/EmailBundle
- Change php version in `composer.json`
- Run PHP CS-Fixer (18/07/2017)
- Add of a Method + config value `archiveUser` to allow archiving of user when deleting account (18/07/2017)
- Remove of .travis.yml as tests have to be defined before
- Re-write ChangePassword, Profile and Security Controller methods to integrate the method setUserData() (19/07/2017)
(These methods have to be removed if https://github.com/FriendsOfSymfony/FOSUserBundle/pull/2587 is merged)

v1.3
----
- Correction of gravatar as an option as it didn't work (08/07/2017)
- Use of `userfiles_signout` in `alreadySignedIn.html.twig`
- Remove of field avatar from show/edit profile as it can not be edited and brings no value information to the user
- Add config value `logoutRoute` to define the logoutRoute
- Add redirection of confirmed user (via the link in the email) to dashboard
- Rename of services to respect Symfony' best practice (09/07/2017)
- Add information about overriding Entity `User` to add fields
- Add information about overriding Forms
- Add information about overriding Controller
- Add of a title above Profile actions in the dashboard
- Remove 'data_class' options from Form Types
- Add methods to easily add Actions for delete and signout by overriding them
- Override emails templates for Registration and Resetting Password, to replace user.username by user.firstname
- Remove of main div in templates as this has to be done on the website side (10/07/2017)
- Add of config value `hwiOauth` to be able to display buttons on the login page without having to override it
- Use of fork "https://github.com/975L/FOSUserBundle" to include `setUserData()` Method

v1.2.3
------
- Add use of gravatar options on forgotten templates (07/07/2017)

v1.2.2
------
- Update README.md (07/07/2017)
- Add test on user in `dashboarActions.html.twig`

v1.2.1
------
- Add use of gravatar as an option (07/07/2017)
- Make xml in translation xlf files human-readable

v1.2
----
- Update README.md about informatoin in `security.yml` (06/07/2017)
- Rename `app/Resources/views/user` to `pages` [BC]
- Add possibility to add own site actions to dashboard
- Suppression of un-needed translations and re-numbering (07/07/2017)

v1.1
----
- Update of README.md (04/07/2017)
- Add of code files

v1.0.1 & 1.0.2
--------------
- Correction in composer.json (04/07/2017)

v1.0
----
- Creation of bundle (04/07/2017)