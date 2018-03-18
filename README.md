# Central Authentication System
I am playing with the idea of a central authentication system. This may be really bad, this may be really nice. Who is to care? I am just doing this as a proof of concept for myself and learn more about PHP (ambitious amirite).

This was built with the idea of using an ingame Spigot plugin so users can do do a command such as `/tokengen` and get presented with a URL and a unique token. The user can then complete registration. Through this, users not only have the convenience of one authentication system, they also are immediately verified as the owner of that Mojang acccount.

The end-goal in this project is to ease the headaches that administration can have when dealing with multiple accounts across systems. Also 100% tied accounts to ingame accounts :D

### To Do
- [X] Structure verify.php
- [X] Setup PDO and purge all other db handlers
- [X] Build my own handler on top of PDO ^
- [X] Setup parameterized mysql statements
- [X] Add a proper wildcard login cookies
- [ ] Implement the mailing function
- [X] Implement the setup script
- [X] Finalize table structure
- [ ] Make it work (95% ish)
- [ ] Prettify the UI (95% ish)
- [ ] Setup the API
