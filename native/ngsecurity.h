#ifndef NGSECURITY_H
#define NGSECURITY_H

#ifdef _WIN32
    #define NGSEC_EXPORT extern "C" __declspec(dllexport)
#else
    #define NGSEC_EXPORT extern "C" __attribute__((visibility("default")))
#endif

#include <stddef.h>
#include <stdint.h>

/*
 * ngsecurity - native security primitives for NGuliner.
 * Compiled as a shared library and loaded via PHP FFI.
 */

NGSEC_EXPORT const char *ng_version(void);

/* SHA-256, hex-encoded. Returns 0 on success, -1 if buffer too small. */
NGSEC_EXPORT int ng_sha256_hex(const uint8_t *msg, size_t msg_len, char *out, size_t cap);

/* HMAC-SHA256 raw (32 bytes) and hex (64 chars + NUL). */
NGSEC_EXPORT int ng_hmac_sha256_raw(const uint8_t *key, size_t key_len,
                                    const uint8_t *msg, size_t msg_len, uint8_t *out32);
NGSEC_EXPORT int ng_hmac_sha256_hex(const uint8_t *key, size_t key_len,
                                    const uint8_t *msg, size_t msg_len, char *out, size_t cap);

/* Constant-time comparison. Returns 1 if equal, 0 otherwise. */
NGSEC_EXPORT int ng_constant_time_eq(const uint8_t *a, size_t a_len,
                                     const uint8_t *b, size_t b_len);

/* Cryptographically secure random bytes (BCryptGenRandom / getrandom). */
NGSEC_EXPORT int ng_secure_bytes(uint8_t *out, size_t n);

#endif
