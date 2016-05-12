# Security Service Site

Provides endpoints for producing impossible to unhash hashes for remote systems.

Allows secrets to be kept internally on a hardened redis box rather than on a web application server.

Uses bcrypt and hmac-sha256 to ensure secrecy.

No passwords or hashes are stored in the system.