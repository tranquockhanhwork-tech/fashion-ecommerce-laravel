from pathlib import Path

pdf_path = Path(r"c:\Users\Admin\Downloads\Tran-Quoc-Khanh-CV.pdf")

try:
    from pypdf import PdfReader
except Exception as exc:
    print(f"IMPORT_ERROR: {exc}")
    raise SystemExit(1)

reader = PdfReader(str(pdf_path))
for i, page in enumerate(reader.pages, start=1):
    text = page.extract_text() or ""
    print(f"--- PAGE {i} ---")
    print(text)
