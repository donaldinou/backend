set :application, "backoffice"
set :domain,      "vlboec2"
set :user, "viteloge"
set :deploy_to,   "/home/viteloge/backoffice"
set :app_path,    "app"

set :shared_files,      ["app/config/parameters.yml"]
set :use_composer, true

set :repository,  "file:///home/acreat/src/viteloge/new_admin"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`
set   :deploy_via,    :copy

set :model_manager, "doctrine"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain                         # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where Symfony2 migrations will run

set :writable_dirs,     ["app/cache", "app/logs"]
set :webserver_user,    "apache"
set :permission_method, :acl
set :use_set_permissions, true

set   :use_sudo,      false
set  :keep_releases,  3

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL
