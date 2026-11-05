import zlib
import re

with open('test_invoice.pdf', 'rb') as f:
    pdf_data = f.read()

stream_pattern = re.compile(b'stream[\r\n]+(.*?)[\r\n]+endstream', re.DOTALL)
streams = stream_pattern.findall(pdf_data)

decompressed = zlib.decompress(streams[0]).decode('latin1', errors='ignore')

# Find where /I1 Do is
idx1 = decompressed.find('/I1 Do')
if idx1 != -1:
    print("--- Context around /I1 Do ---")
    print(decompressed[max(0, idx1-200):idx1+200])

idx2 = decompressed.find('/I2 Do')
if idx2 != -1:
    print("--- Context around /I2 Do ---")
    print(decompressed[max(0, idx2-200):idx2+200])

