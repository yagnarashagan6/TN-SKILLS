<?php
// tnskills_table.php
// Shows submissions in a table. Reads from 'tnskills_submissions.csv' if present; otherwise uses sample data.

// Helper to escape output
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

$csvFile = __DIR__ . DIRECTORY_SEPARATOR . 'tnskills_submissions.csv';
$submissions = [];

if (is_readable($csvFile)) {
    if (($h = fopen($csvFile, 'r')) !== false) {
        $headers = fgetcsv($h);
        while (($row = fgetcsv($h)) !== false) {
            $item = [];
            foreach ($headers as $i => $key) {
                $item[$key] = $row[$i] ?? '';
            }
            // normalize skills into array
            if (isset($item['skills'])) {
                $item['skills'] = array_map('trim', explode('|', $item['skills']));
            }
            $submissions[] = $item;
        }
        fclose($h);
    }
}

// If no CSV found or empty, use sample data
if (empty($submissions)) {
    $submissions = [
        [
            'full_name' => 'Aisha Khan',
            'email' => 'aisha@example.com',
            'phone' => '+91 9876543210',
            'experience' => '2',
            'skills' => ['HTML','CSS','JavaScript'],
            'portfolio' => 'https://portfolio.example.com/aisha',
            'cover' => 'I build responsive websites.',
            'date' => date('Y-m-d')
        ],
        [
            'full_name' => 'Carlos Mendes',
            'email' => 'carlos@example.org',
            'phone' => '+1 555-123-4567',
            'experience' => '5',
            'skills' => ['PHP','MySQL'],
            'portfolio' => '',
            'cover' => 'Backend-focused developer.',
            'date' => date('Y-m-d', strtotime('-2 days'))
        ]
    ];
}

// Export CSV on request (generates from current $submissions)
if (isset($_GET['export']) && $_GET['export'] == '1') {
    $filename = 'tnskills_submissions_export_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $out = fopen('php://output', 'w');
    // headers
    $cols = ['full_name','email','phone','experience','skills','portfolio','cover','date'];
    fputcsv($out, $cols);
    foreach ($submissions as $s) {
        $row = [];
        foreach ($cols as $c) {
            $val = $s[$c] ?? '';
            if (is_array($val)) $val = implode('|', $val);
            $row[] = $val;
        }
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>TNSkills — Submissions Table</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;max-width:1100px;margin:18px auto;padding:0 12px;color:#222}
        table{width:100%;border-collapse:collapse;margin-top:12px}
        th,td{padding:8px 10px;border:1px solid #ddd;text-align:left}
        th{background:#f5f7fa;cursor:pointer}
        tr:nth-child(even){background:#fafafa}
        .controls{display:flex;gap:8px;align-items:center}
        .search{flex:1}
        .small{font-size:0.9rem;color:#555}
        .tag{display:inline-block;background:#eef;border-radius:3px;padding:2px 6px;margin:2px 2px;font-size:0.85rem}
        @media (max-width:760px){table, thead, tbody, th, td, tr{display:block} th{position:sticky;top:0}}
    </style>
</head>
<body>
    <h1>TNSkills — Submissions</h1>
    <div class="controls">
        <input id="q" class="search" type="search" placeholder="Search name, email, skills...">
        <a href="?export=1"><button>Export CSV</button></a>
        <button id="reset">Reset</button>
    </div>

    <table id="subs">
        <thead>
            <tr>
                <th data-key="full_name">Name</th>
                <th data-key="email">Email</th>
                <th data-key="phone">Phone</th>
                <th data-key="experience">Exp (yrs)</th>
                <th data-key="skills">Skills</th>
                <th data-key="portfolio">Portfolio</th>
                <th data-key="date">Date</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($submissions as $s): ?>
            <tr>
                <td><?= e($s['full_name'] ?? '') ?></td>
                <td><a href="mailto:<?= e($s['email'] ?? '') ?>"><?= e($s['email'] ?? '') ?></a></td>
                <td><?= e($s['phone'] ?? '') ?></td>
                <td class="small"><?= e($s['experience'] ?? '') ?></td>
                <td>
                    <?php
                        $ks = $s['skills'] ?? [];
                        if(is_array($ks)){
                            foreach($ks as $k) echo '<span class="tag">'.e($k).'</span>';
                        } else {
                            // if string, split by pipe
                            foreach(explode('|', $ks) as $k) if(trim($k) !== '') echo '<span class="tag">'.e($k).'</span>';
                        }
                    ?>
                </td>
                <td><?php if(!empty($s['portfolio'])): ?><a href="<?= e($s['portfolio']) ?>" target="_blank">Link</a><?php endif; ?></td>
                <td><?= e($s['date'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    // Simple client-side search and column sort
    (function(){
        const q = document.getElementById('q');
        const tbody = document.querySelector('#subs tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const reset = document.getElementById('reset');

        q.addEventListener('input', function(){
            const term = q.value.trim().toLowerCase();
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.indexOf(term) === -1 ? 'none' : '';
            });
        });

        reset.addEventListener('click', function(){ q.value=''; q.dispatchEvent(new Event('input')); });

        // Sorting
        const ths = document.querySelectorAll('#subs thead th');
        ths.forEach((th, idx) => {
            th.addEventListener('click', () => {
                const key = th.dataset.key || idx;
                const dir = th.dataset.dir === 'asc' ? -1 : 1;
                th.dataset.dir = dir === 1 ? 'asc' : 'desc';
                const sorted = rows.sort((a,b) => {
                    const A = a.children[idx].textContent.trim().toLowerCase();
                    const B = b.children[idx].textContent.trim().toLowerCase();
                    if(!isNaN(A) && !isNaN(B)) return (Number(A)-Number(B)) * dir;
                    return A < B ? -1*dir : (A > B ? 1*dir : 0);
                });
                // re-append in new order
                sorted.forEach(r => tbody.appendChild(r));
            });
        });
    })();
    </script>
</body>
</html>
