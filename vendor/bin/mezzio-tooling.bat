@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../mezzio/mezzio/bin/mezzio-tooling
php "%BIN_TARGET%" %*
