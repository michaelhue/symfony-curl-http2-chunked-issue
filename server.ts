import * as selfsigned from "https://deno.land/x/selfsignedeno@v2.1.1-deno/index.js";

function certificate() {
  console.log("Generating self-signed certificate");

  const { cert, private: key } = selfsigned.generate(
    [{ name: "commonName", value: "localhost" }],
    { keySize: 2048 }
  );

  return { cert, key };
}

Deno.serve({ port: 8000, ...certificate() }, async (request) => {
  const data = await request.formData();

  console.log("");
  console.log(`${request.method} ${request.url}`);
  console.log(request.headers);
  console.log(data);

  return new Response(data.get("foo"));
});
