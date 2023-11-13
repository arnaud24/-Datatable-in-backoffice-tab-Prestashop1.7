<div class="panel">
    <h3>Listes de Naissance</h3>
    <table id="listenaissancetable" class="table table-bordered">
        <thead>
            <tr>
                <th>ID: LISTE DE NAISSANCE</th>
                <th>ID: CLIENT</th>
                <th>EMAIL</th>
                <th>PRENOM</th>
                <th>NOM</th>
                <th>PRODUIT</th>
                <th>DECLINAISON</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$listenaissance_data item=listenaissance}
                <tr>
                    <td>{$listenaissance.id_liste_naissance}</td>
                    <td>{$listenaissance.id_client}</td>
                    <td>{$listenaissance.email_client}</td>
                    <td>{$listenaissance.prenom_client}</td>
                    <td>{$listenaissance.nom_client}</td>
                    <td>{$listenaissance.nom_produit}</td>
                    <td>{$listenaissance.declinaison_produit}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>

