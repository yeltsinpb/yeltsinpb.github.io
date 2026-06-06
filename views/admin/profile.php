<form method="post" action="<?= url('/admin/profile') ?>" enctype="multipart/form-data" class="form-card">
    <?= csrf_field() ?>

    <h2 class="form-section">Account</h2>
    <div class="field">
        <label for="account_email">Account email <small>(used to log in)</small></label>
        <input id="account_email" name="account_email" type="email"
               value="<?= e(old('account_email', $user['email'])) ?>" required>
        <small>Changing this updates your login credential, not the public contact email below.</small>
    </div>

    <h2 class="form-section">Identity</h2>
    <div class="field">
        <label for="name">Display name</label>
        <input id="name" name="name" type="text" value="<?= e($profile['name']) ?>" required>
    </div>
    <div class="field">
        <label for="tagline">Tagline</label>
        <input id="tagline" name="tagline" type="text" value="<?= e($profile['tagline']) ?>" required>
        <small>The one-liner that lives under your name on the homepage.</small>
    </div>
    <div class="field">
        <label for="bio">Bio</label>
        <textarea id="bio" name="bio" rows="6" required><?= e($profile['bio']) ?></textarea>
    </div>

    <h2 class="form-section">Contact</h2>
    <div class="field-row">
        <div class="field">
            <label for="email">Public contact email</label>
            <input id="email" name="email" type="email" value="<?= e($profile['email']) ?>" required>
        </div>
        <div class="field">
            <label for="location">Location</label>
            <input id="location" name="location" type="text" value="<?= e($profile['location']) ?>">
        </div>
    </div>

    <h2 class="form-section">Social links</h2>
    <div class="field-row">
        <div class="field">
            <label for="github_url">GitHub</label>
            <input id="github_url" name="github_url" type="url" value="<?= e($profile['github_url']) ?>" placeholder="https://github.com/...">
        </div>
        <div class="field">
            <label for="linkedin_url">LinkedIn</label>
            <input id="linkedin_url" name="linkedin_url" type="url" value="<?= e($profile['linkedin_url']) ?>" placeholder="https://linkedin.com/in/...">
        </div>
    </div>
    <div class="field-row">
        <div class="field">
            <label for="twitter_url">Twitter / X</label>
            <input id="twitter_url" name="twitter_url" type="url" value="<?= e($profile['twitter_url']) ?>" placeholder="https://twitter.com/...">
        </div>
        <div class="field">
            <label for="dribbble_url">Dribbble</label>
            <input id="dribbble_url" name="dribbble_url" type="url" value="<?= e($profile['dribbble_url']) ?>" placeholder="https://dribbble.com/...">
        </div>
    </div>

    <h2 class="form-section">Avatar</h2>
    <?php if (!empty($profile['avatar'])): ?>
        <div class="avatar-preview">
            <img src="<?= e(url('/uploads/' . $profile['avatar'])) ?>" alt="Current avatar">
        </div>
    <?php endif; ?>
    <div class="field">
        <label for="avatar">Upload new (jpg, png, webp, max 5MB)</label>
        <input id="avatar" name="avatar" type="file" accept="image/*">
    </div>

    <div class="form-actions">
        <button type="submit" class="cta"><span>Save changes</span><span class="cta__arrow">→</span></button>
    </div>
</form>

<form method="post" action="<?= url('/admin/password') ?>" class="form-card" style="margin-top:2rem">
    <?= csrf_field() ?>

    <h2 class="form-section">Change password</h2>
    <div class="field">
        <label for="current_password">Current password</label>
        <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
    </div>
    <div class="field">
        <label for="new_password">New password</label>
        <input id="new_password" name="new_password" type="password" required autocomplete="new-password">
        <small>Minimum 8 characters.</small>
    </div>
    <div class="field">
        <label for="confirm_password">Confirm new password</label>
        <input id="confirm_password" name="confirm_password" type="password" required autocomplete="new-password">
    </div>

    <div class="form-actions">
        <button type="submit" class="cta"><span>Update password</span><span class="cta__arrow">→</span></button>
    </div>
</form>
