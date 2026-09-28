<?php

/**
 * Objectives
 * - Review form processing with an email field.
 * - Identify when a regular expression is useful.
 * - Build and test a regex for the equipment tag EQ-1234.
 * - Reference: textbook pages 212-215.
 */

$errors = [];
$email = '';
$assetTag = '';
$submittedSuccessfully = false;

// On the first GET request, PHP skips this block and displays the blank form.
// After the form is submitted with POST, PHP retrieves and validates the input.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*** Quick review: retrieve and validate an email address. ***/

    // PRESERVE the email entered so an invalid entry can remain in the sticky field
    $email = trim(filter_input(INPUT_POST, 'email') ?? '');

    // VALIDATE the email
    // FILTER_VALIDATE_EMAIL returns:
    // - the email string when it is valid;
    // - false when it is invalid; or
    // - null when the field was not submitted.
    $emailResult = filter_input(
        INPUT_POST,
        'email',
        FILTER_VALIDATE_EMAIL
    );
    // A valid email returns the email string. Otherwise, the result is falsy.
    if (!$emailResult) {
        $errors['email'] = 'Enter a valid college email address.';
    }

    $assetTag = strtoupper(
        trim(filter_input(INPUT_POST, 'asset_tag'))
    );

    // ***** NEW: Regular Expressions - pattern matching  ************

    if (!$assetTag) {
        $errors['asset_tag'] = 'Equipment tag is required.';
    } else {
        
        /*** Build the regular expression. ***/
        // /  /   delimit the regular expression
        // ^      start of the value
        // EQ-    exact characters
        // [0-9] one digit from 0 through 9
        // {4}    exactly four digits
        // $      end of the value
        // Practice in the live editor: https://regex101.com/
        $tagPattern = "/^EQ-\d{4}$/";
        $tagResult = preg_match($tagPattern, $assetTag);
        // echo $tagResult;
        /*** Use preg_match() and save its result. ***/
        // 1 means match, 0 means nonmatch
        if ($tagResult === 0) {
            $errors['asset_tag'] = 'Use the format EQ-1234';
        }

        

        /*** Step 5: Add an error when the value does not match. ***/
        
    }

    if (empty($errors)) {
        $submittedSuccessfully = true;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Equipment Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-5" style="max-width: 700px;">
        <h1>Equipment Request</h1>
        <p class="lead">Enter your email and the tag printed on the equipment.</p>

        <?php if ($submittedSuccessfully): ?>
            <div class="alert alert-success" role="status">
                Your equipment request was received.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <h2 class="h5">Please correct the following:</h2>
                <ul class="mb-0">
                    <!-- REVIEW: Loop through the $errors array created when validating -->
                    <?php foreach ($errors as $message): ?>
                        <li><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- REVIEW: Sticky field value is storing the submitted text -->
        <form method="post" action="equipment-request.php">
            <div class="mb-3">
                <label class="form-label" for="email">College email</label>
                <!-- REVIEW changing Bootstrap classes for visual feedback -->
                <input
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label" for="asset_tag">Equipment tag (eg: EQ-1234)</label>
                <input
                    class="form-control <?= isset($errors['asset_tag']) ? 'is-invalid' : '' ?>"
                    type="text"
                    id="asset_tag"
                    name="asset_tag"
                    value="<?= htmlspecialchars($assetTag, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['asset_tag'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['asset_tag'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <button class="btn btn-primary" type="submit">Submit request</button>
        </form>
    </main>
</body>

</html>