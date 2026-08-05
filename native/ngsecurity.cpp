#include "ngsecurity.h"

#include <string.h>

#ifdef _WIN32
    #ifndef WIN32_LEAN_AND_MEAN
        #define WIN32_LEAN_AND_MEAN
    #endif
    #include <windows.h>
    #include <bcrypt.h>
    #pragma comment(lib, "bcrypt.lib")
#else
    #include <sys/random.h>
#endif

/* ------------------------------------------------------------------ */
/* SHA-256 (self-contained, no external crypto dependency)            */
/* ------------------------------------------------------------------ */

namespace {

inline uint32_t rotr32(uint32_t x, uint32_t n) { return (x >> n) | (x << (32 - n)); }

struct Sha256 {
    uint32_t h[8];
    uint64_t total = 0;
    uint8_t  buf[64];
    size_t   buflen = 0;

    static const uint32_t K[64];

    Sha256() {
        h[0] = 0x6a09e667u; h[1] = 0xbb67ae85u; h[2] = 0x3c6ef372u; h[3] = 0xa54ff53au;
        h[4] = 0x510e527fu; h[5] = 0x9b05688cu; h[6] = 0x1f83d9abu; h[7] = 0x5be0cd19u;
    }

    void block(const uint8_t *p) {
        uint32_t w[64];
        for (int i = 0; i < 16; i++) {
            w[i] = ((uint32_t)p[i * 4] << 24) | ((uint32_t)p[i * 4 + 1] << 16) |
                   ((uint32_t)p[i * 4 + 2] << 8) | (uint32_t)p[i * 4 + 3];
        }
        for (int i = 16; i < 64; i++) {
            uint32_t s0 = rotr32(w[i - 15], 7) ^ rotr32(w[i - 15], 18) ^ (w[i - 15] >> 3);
            uint32_t s1 = rotr32(w[i - 2], 17) ^ rotr32(w[i - 2], 19) ^ (w[i - 2] >> 10);
            w[i] = w[i - 16] + s0 + w[i - 7] + s1;
        }
        uint32_t a = h[0], b = h[1], c = h[2], d = h[3], e = h[4], f = h[5], g = h[6], hh = h[7];
        for (int i = 0; i < 64; i++) {
            uint32_t s1  = rotr32(e, 6) ^ rotr32(e, 11) ^ rotr32(e, 25);
            uint32_t ch  = (e & f) ^ (~e & g);
            uint32_t t1  = hh + s1 + ch + K[i] + w[i];
            uint32_t s0  = rotr32(a, 2) ^ rotr32(a, 13) ^ rotr32(a, 22);
            uint32_t maj = (a & b) ^ (a & c) ^ (b & c);
            uint32_t t2  = s0 + maj;
            hh = g; g = f; f = e; e = d + t1; d = c; c = b; b = a; a = t1 + t2;
        }
        h[0] += a; h[1] += b; h[2] += c; h[3] += d;
        h[4] += e; h[5] += f; h[6] += g; h[7] += hh;
    }

    void update(const uint8_t *data, size_t len) {
        total += len;
        while (len > 0) {
            size_t take = 64 - buflen;
            if (take > len) take = len;
            memcpy(buf + buflen, data, take);
            buflen += take;
            data += take;
            len -= take;
            if (buflen == 64) {
                block(buf);
                buflen = 0;
            }
        }
    }

    void final(uint8_t out[32]) {
        uint64_t bits = total * 8;
        uint8_t  pad = 0x80;
        update(&pad, 1);
        uint8_t zero = 0;
        while (buflen != 56) update(&zero, 1);
        uint8_t lenbe[8];
        for (int i = 0; i < 8; i++) lenbe[i] = (uint8_t)(bits >> (56 - i * 8));
        update(lenbe, 8);
        for (int i = 0; i < 8; i++) {
            out[i * 4]     = (uint8_t)(h[i] >> 24);
            out[i * 4 + 1] = (uint8_t)(h[i] >> 16);
            out[i * 4 + 2] = (uint8_t)(h[i] >> 8);
            out[i * 4 + 3] = (uint8_t)h[i];
        }
    }
};

const uint32_t Sha256::K[64] = {
    0x428a2f98u, 0x71374491u, 0xb5c0fbcfu, 0xe9b5dba5u, 0x3956c25bu, 0x59f111f1u, 0x923f82a4u, 0xab1c5ed5u,
    0xd807aa98u, 0x12835b01u, 0x243185beu, 0x550c7dc3u, 0x72be5d74u, 0x80deb1feu, 0x9bdc06a7u, 0xc19bf174u,
    0xe49b69c1u, 0xefbe4786u, 0x0fc19dc6u, 0x240ca1ccu, 0x2de92c6fu, 0x4a7484aau, 0x5cb0a9dcu, 0x76f988dau,
    0x983e5152u, 0xa831c66du, 0xb00327c8u, 0xbf597fc7u, 0xc6e00bf3u, 0xd5a79147u, 0x06ca6351u, 0x14292967u,
    0x27b70a85u, 0x2e1b2138u, 0x4d2c6dfcu, 0x53380d13u, 0x650a7354u, 0x766a0abbu, 0x81c2c92eu, 0x92722c85u,
    0xa2bfe8a1u, 0xa81a664bu, 0xc24b8b70u, 0xc76c51a3u, 0xd192e819u, 0xd6990624u, 0xf40e3585u, 0x106aa070u,
    0x19a4c116u, 0x1e376c08u, 0x2748774cu, 0x34b0bcb5u, 0x391c0cb3u, 0x4ed8aa4au, 0x5b9cca4fu, 0x682e6ff3u,
    0x748f82eeu, 0x78a5636fu, 0x84c87814u, 0x8cc70208u, 0x90befffau, 0xa4506cebu, 0xbef9a3f7u, 0xc67178f2u
};

void sha256(const uint8_t *msg, size_t len, uint8_t out[32]) {
    Sha256 s;
    s.update(msg, len);
    s.final(out);
}

void hmac_sha256(const uint8_t *key, size_t key_len,
                 const uint8_t *msg, size_t msg_len, uint8_t out[32]) {
    uint8_t k[64];
    if (key_len > 64) {
        sha256(key, key_len, k);
        memset(k + 32, 0, 32);
    } else {
        memcpy(k, key, key_len);
        memset(k + key_len, 0, 64 - key_len);
    }
    uint8_t ipad[64], opad[64];
    for (int i = 0; i < 64; i++) {
        ipad[i] = k[i] ^ 0x36;
        opad[i] = k[i] ^ 0x5c;
    }
    Sha256 inner;
    inner.update(ipad, 64);
    inner.update(msg, msg_len);
    uint8_t ih[32];
    inner.final(ih);
    Sha256 outer;
    outer.update(opad, 64);
    outer.update(ih, 32);
    outer.final(out);
}

void to_hex(const uint8_t *src, size_t len, char *dst) {
    static const char *HEX = "0123456789abcdef";
    for (size_t i = 0; i < len; i++) {
        dst[i * 2]     = HEX[src[i] >> 4];
        dst[i * 2 + 1] = HEX[src[i] & 0x0f];
    }
}

} // namespace

/* ------------------------------------------------------------------ */
/* Exported C ABI                                                      */
/* ------------------------------------------------------------------ */

extern "C" const char *ng_version(void) {
    return "ngsecurity 1.0.0 (C++17, MSVC)";
}

int ng_sha256_hex(const uint8_t *msg, size_t msg_len, char *out, size_t cap) {
    if (cap < 65) return -1;
    uint8_t d[32];
    sha256(msg, msg_len, d);
    to_hex(d, 32, out);
    out[64] = '\0';
    return 0;
}

int ng_hmac_sha256_raw(const uint8_t *key, size_t key_len,
                       const uint8_t *msg, size_t msg_len, uint8_t *out32) {
    hmac_sha256(key, key_len, msg, msg_len, out32);
    return 0;
}

int ng_hmac_sha256_hex(const uint8_t *key, size_t key_len,
                       const uint8_t *msg, size_t msg_len, char *out, size_t cap) {
    if (cap < 65) return -1;
    uint8_t d[32];
    hmac_sha256(key, key_len, msg, msg_len, d);
    to_hex(d, 32, out);
    out[64] = '\0';
    return 0;
}

int ng_constant_time_eq(const uint8_t *a, size_t a_len, const uint8_t *b, size_t b_len) {
    uint8_t diff = (uint8_t)(a_len ^ b_len);
    size_t n = a_len < b_len ? a_len : b_len;
    for (size_t i = 0; i < n; i++) diff |= (uint8_t)(a[i] ^ b[i]);
    return diff == 0 ? 1 : 0;
}

int ng_secure_bytes(uint8_t *out, size_t n) {
#ifdef _WIN32
    return BCryptGenRandom(NULL, out, (ULONG)n, BCRYPT_USE_SYSTEM_PREFERRED_RNG) == 0 ? 0 : -1;
#else
    size_t done = 0;
    while (done < n) {
        ssize_t r = getrandom(out + done, n - done, 0);
        if (r < 0) return -1;
        done += (size_t)r;
    }
    return 0;
#endif
}
