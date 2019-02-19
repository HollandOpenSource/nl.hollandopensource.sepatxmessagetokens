# nl.hollandopensource.sepatxmessagetokens

This module uses [CiviSEPA's `hook_civicrm_modify_txmessage`](https://github.com/Project60/org.project60.sepa#customisation) to pass the Transaction Message through [CiviCRM's new token processor](https://docs.civicrm.org/dev/en/latest/framework/token/#token-processor).

The following tokens are added:

- sepa.frequencyUnit
- sepa.frequencyInterval
- sepa.financialTypeId

## Examples

Example Transaction Message: `{capture assign=u}{sepa.frequencyUnit}{/capture}{capture assign=i}{sepa.frequencyInterval}{/capture}{capture assign=f}{sepa.financialTypeId}{/capture}Thanks for your {if $i==1}monthly{elseif $i==3}quarterly{elseif $i==6}half-yearly{elseif $i==12}yearly{/if} contribution {if $f==12}for project 12{elseif $f==13}for project 13{else}for our important work{/if}.`

Example result: Thanks for your quarterly contribution for project 12.

With spacing for better reading:

```
{capture assign=u}{sepa.frequencyUnit}{/capture}
{capture assign=i}{sepa.frequencyInterval}{/capture}
{capture assign=f}{sepa.financialTypeId}{/capture}

Thanks for your 

{if $i==1}monthly
{elseif $i==3}quarterly
{elseif $i==6}half-yearly
{elseif $i==12}yearly
{/if}

contribution

{if $f==12}for project 12
{elseif $f==13}for project 13
{else}for our important work
{/if}

.
```
