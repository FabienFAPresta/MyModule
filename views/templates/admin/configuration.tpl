{if $message != null}
    <div class="alert alert-success" role="alert">
        <p class="alert-text">
            {$message}
        </p>
    </div>
{/if}

<form action="" method="post">
    <div class="form-group">
        <label class="form-control-label" for="courserating">Course rating</label>
        <input type="text" class="form-control" id="courserating" name="courserating" required
            value="{$courserating}" />
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Validate</button>
    </div>
</form>