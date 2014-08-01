githooks
========

Add pre-receive Hooks (or other) to your project for checking pushed code.


## Installation

### 1. Setup githooks
Go to your git repository (server):
```bash
cd /home/git/repositories/<reponame>.git/hooks
```

Make a new directory for your own hooks
```bash
mkdir githooks
cd githooks
```

Now put following in your composer.json:
```bash
{
        "require": {
                "sseidelmann/githooks": "dev-master"
        }
}
```

Now install/update githooks:
```bash
composer install
composer update
```

### 2. Create your hook config

Put this in `githooks/config.json`

```json
{
    "hooks": {
        "pre-receive": {
            "\\GitHooks\\Hooks\\PHPLintHook":{
                "priority": "0"
            },
            "\\GitHooks\\Hooks\\PHPCSHook":{
                "priority": "10",
                "standard": "PSR2"
            }
        }
    }
}
```


### 3. Register your hook

Go back to git's hook directory (`cd /home/git/repositories/<reponame>.git/hooks`). Edit the `pre-receive` file.

```
#!/bin/bash
./$SCRIPT/hooks/githooks/vendor/bin/hook check pre-receive $1 $2 $3
exit $?
```