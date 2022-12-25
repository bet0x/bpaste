# BarraHome Pastebin service

### Based on:
* [Lazer Database] (https://github.com/Lazer-Database/Lazer-Database)
* [cmdpb] (https://github.com/LaisRast/cmdpb)

A simple private command-line pastebin that uses HTTP basic authentication and Lazer Database (Flat-File).

## Get started

### Client-side:
You can communicate with the pastebin using `curl`
or using the shell script `bpaste` (see below).
There are two options to authenticate using `curl`:

* `curl -n`: which reads login credentials from your `~/.netrc`.
  You can enter your login credentials to `~/.netrc` like this:
  ```
  machine example.com login USERNAME password PASSWORD
  ```
  
* `curl -u USERNAME:PASSWORD`: by providing login credentials in each call.


## Usage
Posting a new paste

* from a `FILE`:
  ```
  curl -n -F "c=@FILE" https://paste.barrahome.org/index.php
  ```
  
* From `stdin`:
  ```
  echo Hello world | curl -n -F "c=<-" https://paste.barrahome.org/index.php
  ```
  
* from a string:
  ```
  curl -n -F "c=Hello world" https://paste.barrahome.org/index.php
  ```

Getting all pastes:
```
curl -n https://paste.barrahome.org/index.php
```

Getting the paste with `id=ID`:
```
curl -n "https://paste.barrahome.org/index.php?id=ID"
```

Deleting the paste with `id=ID`:
```
curl -n -X DELETE "https://paste.barrahome.org/index.php?id=ID"
```

Updating the paste with `id=ID`:
```
curl -n -F "c=Hello world" "https://paste.barrahome.org/index.php?id=ID"
curl -n -F "c=@file" "https://paste.barrahome.org/index.php?id=ID"
echo Hello world | curl -n -F "c=<-" "https://paste.barrahome.org/index.php?id=ID"
```

## Shell script
A shell script `bpaste` is also provided.
To start using it,
download it,
make it executable
and edit it to make the variable `URL` points to your `index.php`.

Examples of use:
```
bpaste                         # post from what you write
bpaste file                    # post from file
echo hello world | bpaste      # post from stdin
bpaste -a                      # show all pastes
bpaste -s ID                   # show paste with id=ID
bpaste -d ID                   # delete paste with id=ID
bpaste -u ID                   # update paste with id=ID
```

