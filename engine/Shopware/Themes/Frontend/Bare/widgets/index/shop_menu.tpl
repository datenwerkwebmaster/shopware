{* Language switcher *}
{block name='frontend_index_actions_active_shop'}
    {if $shop && $languages|count > 1}
        <div class="top-bar--language">
            {if $languages|count > 1}
                <form method="post" action="{$smarty.server.REQUEST_URI|escape}" class="language--form">
                    <div class="field--select">
                        <select name="__shop" class="language--select" data-auto-submit-form="true">
                            {foreach $languages as $language}
                                <option value="{$language->getId()}" {if $language->getId() === $shop->getId()}selected="selected"{/if}>
                                    {$language->getName()}
                                </option>
                            {/foreach}
                        </select>
                        <span class="arrow"></span>
                    </div>
                </form>
            {/if}
        </div>
    {/if}
{/block}

{* Currency changer *}
{block name='frontend_index_actions_currency'}
    {if $currencies|count > 1}
        <div class="top-bar--currency">
			<form action="{$smarty.server.REQUEST_URI|escape}" method="post" class="currency--form">
                <div class="field--select">
                    <select name="__currency" class="currency--select" data-auto-submit-form="true">
                        {foreach $currencies as $currency}
                            <option value="{$currency->getId()}"{if $currency->getId() === $shop->getCurrency()->getId()} selected="selected"{/if}>
                                {$currency->getSymbol()} {$currency->getCurrency()}
                            </option>
                        {/foreach}
                    </select>
                    <span class="arrow"></span>
                </div>
			</form>
        </div>
    {/if}
{/block}