import os

EXCLUDE = {"vendor", "node_modules", ".git", "storage", "bootstrap/cache"}
EXTENSIONS = (".php", ".js", ".ts", ".vue", ".json", ".blade.php")

with open("repo_snapshot.txt", "w", encoding="utf-8") as out:
    for root, dirs, files in os.walk("."):
        dirs[:] = [d for d in dirs if d not in EXCLUDE]

        for file in files:
            if file.endswith(EXTENSIONS):
                path = os.path.join(root, file)

                out.write(f"\n===== FILE: {path} =====\n")

                try:
                    with open(path, "r", encoding="utf-8") as f:
                        out.write(f.read())
                except:
                    out.write("[UNREADABLE FILE]\n")

                out.write("\n\n")
