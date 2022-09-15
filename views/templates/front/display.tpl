{extends file="page.tpl"}

{block name="page_content"}
    Welcome to my shop !
    {*debug*}
    {* {dump($cart)} *}

    <ul>
        <li><strong>$urls.base_url</strong> : {$urls.base_url}</li>
        <li><strong>$urls.current_url</strong> : {$urls.current_url}</li>
    </ul>
{/block}