<?php

class JWTCodec
{
    public function encode(array $payload): string
    {
        $header = json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]);

        //Encoding header and payload 
        $header = $this->base64urlEncode($header);

        $payload = json_encode($payload);
        $payload = $this->base64urlEncode($payload);

        //Generate a keyed hash value using the HMAC method
        //True is for getting binary value
        $signature = hash_hmac("sha256",
                                $header . "." . $payload,
                                "c1c4578b254ac5181c5ec5845ef51c7795de3779f0c123ec0b4c533710503054,
                                true");
        $signature = $this->base64urlEncode($signature);
        
        return $header . "." . $payload . "." . $signature;
    }

    private function base64urlEncode(string $text): string
    {
        return str_replace(
            ["+", "/", "="],
            ["-", "_", ""],
            base64_encode($text)
        );    
    }
}