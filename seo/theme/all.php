<?

$info = mod::service("seo/domainInfo");

<div style='padding:20px;' >

    <table class='u7zk057dj0' >
        foreach(seo_domain::all() as $domain) {
            <tr>
            
                <td>
                    <a href='/seo/domain/id/{$domain->id()}/'>{$domain->title()}</a>
                </td>
                
                <td>
                    echo $info->info($domain->data("domain"),"cy");
                </td>
                
                <td>
                    <a href='{$domain->editor()->url()}' >ред</a>
                </td>
            
                <td>
                    echo $domain->queriesInTop()->count();
                    echo " / ";
                    echo $domain->queries()->count();
                </td>
            
                <td>
                    echo $domain->primaryEngine()->title();
                </td>
        
            </tr>
        }
    </table>
    
    $all = seo_query::all()->count();
    $scanned = seo_query::all()->eq("date(update)",util::now()->notime())->count();
    $percent = floor($scanned / $all*100);
    
    <br/><br/>
    echo "Сканировано ".$percent."%";

</div>
