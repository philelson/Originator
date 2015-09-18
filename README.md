# Originator has now been discontinued.
_This is because the issue is void in Magento 2 as modules can be added via composer and you can get around this issue in Magento 1 with a couple of simple scripts - I assume all your modules are already in seperate repositories!_

1) sync.sh where you add your Magento's install dependant modules via rsync

```bash
#¡/bin/bash
# Repeat for all modules
rsync -vaz /path/to/module/root /path/to/magento/root
```

2) clean.sh because rsync doesn't handle the deletes (sync, delete a file in the module, sync, file file will not be deleted in Magento). This should be run periodically.

```bash
#¡/bin/bash
# Remove all the code
rm -rf /path/to/magento/root
rsync -vaz /path/to/clean/magento /path/to/magento/root
chmod -R 777 app/etc media var includes
./sync.sh
```

<!--

# originator
This will hopefully be a package to help manage Magento modules across projects

# Logic behind the design.
Before 'originator' we used to rsync our modules into the code pool, the main issue is if you delete a file in the
module, it stays in the destination. The --delete flag doesn't work because it tries to delete every file inside
Magento that's not in the original module destination.

## Originator solution

### Single code copy
Chances are if you're a developer you want to checkout your modules into a single location and use them across multiple
Magento projects. We suggest an absolute or relative path that will be consistent across environments.

### Use GIT
When originator iterates over your projects originator.json file it will check if the module exists, if it doesn't
you'll be prompted about it and the program will exist.

If the module does exist, you can configure originator to pull the code from the branch.

### Rsync --delete correction
Fixing the --delete correction requires a cache of all files within the module from start to finish. To make it
environment agnostic this will be stores in a simple file, rather than a database. This will be done
in the modules '.originator_file_cache' which will simple list relative paths from the module root to all files.
If they no longer exists in the module but exist in the destination, then they are removed from the destination.

### Module configuration
Modules themselves don't need any configuration, it is the application that needs configuration. The originator.yml specifies
the relative paths to the required modules. When 'originator run' is executed it will merge the modules from those paths
into the magento_root (default public). During this execution the '.originator_file_cache' file will be updated with any new
files / directories. Originator will also update the modules '.originator_projects' file which contains absolute paths
to the current projects which use this module. This allows developers to call 'originator -module-update' from the module directory which
will tell the application which modules need a re-merge (Adds name to '.originator_module_status') or call
'originator -module-force-merge' to force a re-merge on all projects which use that module.

## Terminology

1. Application|Magento - this is the Magento application
2. Module - is a Magento module to be merged into the application, this is generally stored externally to the
application and merged in

## Committing

### Application

* Commit the 'originator.yml' file.
* Commit any new source files after originator has run into the magento_root directory as you would.
* Don't commit the '.originator_module_status' file.

### Modules

* Commit the '.originator_file_cache' file as this is the primary module history file and if deleted can then be reinstated
from VC.
* Don't commit the '.originator_projects', this is a platform specific project location file.




-->


