<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; }
        body { margin: 0; background: #f4f7fb; color: #172033; }
        main { max-width: 760px; margin: 48px auto; padding: 0 20px; }
        .eyebrow { color: #315ea8; font-size: 13px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 8px 0 4px; font-size: 32px; }
        .lead { color: #526079; margin: 0 0 24px; }
        .card { background: #fff; border: 1px solid #dfe6f0; border-radius: 12px; padding: 22px; margin-bottom: 24px; }
        label { display: block; font-weight: 700; color: #45526a; margin: 14px 0 6px; }
        input, textarea { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #cdd6e4;
            border-radius: 8px; font-size: 15px; font-family: inherit; }
        textarea { min-height: 96px; resize: vertical; }
        button { margin-top: 18px; background: #315ea8; color: #fff; border: 0; border-radius: 8px;
            padding: 11px 22px; font-size: 15px; font-weight: 700; cursor: pointer; }
        .flash-ok { background: #e6f6ec; border: 1px solid #a6e0bd; color: #08783e; padding: 10px 14px;
            border-radius: 8px; margin-bottom: 16px; }
        .flash-err { background: #fdecec; border: 1px solid #f3b7b7; color: #b42318; padding: 10px 14px;
            border-radius: 8px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #e7ecf3; font-size: 14px; vertical-align: top; }
        th { color: #66748d; text-transform: uppercase; font-size: 12px; letter-spacing: .06em; }
        .muted { color: #8a94a6; font-size: 13px; }
        a.nav { color: #315ea8; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
<main>
    <div class="eyebrow">CNS 3205 Server Administration</div>
    <h1>Share your feedback</h1>
    <p class="lead">A second form backed by its own <code>feedback</code> table in the <code>Lab2</code> database.</p>

    <div class="card">
        <?php if ($success !== null): ?>
            <div class="flash-ok"><?php echo htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="flash-err">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/feedback" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" maxlength="100" required>
            <label for="email">Email (optional)</label>
            <input type="email" id="email" name="email">
            <label for="message">Message</label>
            <textarea id="message" name="message" required></textarea>
            <button type="submit">Submit feedback</button>
        </form>
    </div>

    <div class="card">
        <h2 style="margin-top:0;font-size:20px;">Submitted feedback</h2>
        <?php if (empty($entries)): ?>
            <p class="muted">No feedback yet. Be the first to submit.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Message</th><th>Submitted</th></tr>
                </thead>
                <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string) $entry['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($entry['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $entry['message'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="muted"><?php echo htmlspecialchars((string) $entry['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <p><a class="nav" href="/">&larr; Back to sign in</a> &nbsp;·&nbsp; <a class="nav" href="/main.php">Gallery</a></p>
</main>
</body>
</html>
