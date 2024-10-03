#!/bin/sh

FILES="proto/protobufs/*.proto"
for f in $(find proto/protobufs -name '*.proto')
do
	if [ -f "$f" ]
	then
		echo "Generating proto for $f"
		protoc --proto_path=proto/protobufs --php_out=proto $f
		#protoc --proto_path=proto/protobufs --js_out=import_style=commonjs,binary:company-app/src/proto $f
	fi
done
