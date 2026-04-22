import os
import glob

def process_css_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    output = ""
    media_blocks = []
    i = 0
    while i < len(content):
        media_match = content.find('@media', i)
        if media_match == -1:
            output += content[i:]
            break

        output += content[i:media_match]

        open_brace_index = content.find('{', media_match)
        if open_brace_index == -1:
            break

        brace_count = 1
        j = open_brace_index + 1
        while j < len(content) and brace_count > 0:
            if content[j] == '{':
                brace_count += 1
            elif content[j] == '}':
                brace_count -= 1
            j += 1

        block = content[media_match:j]
        media_blocks.append(block)
        i = j

    if media_blocks:
        new_content = output.strip() + '\n\n/* === @MEDIA KONTSULTAK === */\n' + '\n\n'.join(media_blocks) + '\n'
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Processed: {os.path.basename(file_path)} ({len(media_blocks)} blocks moved)")

if __name__ == "__main__":
    css_files = glob.glob('*.css')
    for f in css_files:
        if f != 'normalize.css':
            process_css_file(f)
