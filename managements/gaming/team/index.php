<?php
/**
 * Bel-CMS [Content management system]
 * @version 1.0.0
 * @link https://bel-cms.be
 * @link https://determe.be
 * @license http://opensource.org/licenses/GPL-3.-copyleft
 * @copyright 2014-2019 Bel-CMS
 * @author as Stive - stive@determe.be
 */

if (!defined('CHECK_INDEX')) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit(ERROR_INDEX);
}
?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="block full">
		    <div class="block-title">
		        <h2><strong>Liste des Teams</strong></h2>
		    </div>
			<div class="table-responsive">
				<table  class="DataTableBelCMS table table-vcenter table-condensed table-bordered">
					<thead>
						<tr>
							<th># ID</th>
							<th>Nom</th>
							<th>Déscription</th>
							<th>NB° utilisateur</th>
							<th>Options</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th># ID</th>
							<th>Nom</th>
							<th>Déscription</th>
							<th>NB° utilisateur</th>
							<th>Options</th>
						</tr>
					</tfoot>
					<tbody>
					<?php
					foreach ($data as $k => $v):
						?>
						<tr>
							<td><?=$v->id?></td>
							<td><?=$v->name?></td>
							<td><?=$v->description?></td>
							<td><?=$v->count?></td>
							<td>
								<a href="Team/player/<?=$v->id?>?management&gaming=true" class="btn btn-small btn-primary"> <i class="fa fa-plus-square"></i></a>
								<a href="team/edit/<?=$v->id?>?management&gaming=true" class="btn btn-small btn-success"><i class="fa fa-share-square-o"></i></a>
								<a href="#" data-toggle="modal" data-target="#modal_<?=$v->id?>" class="btn btn-danger btn-small"><i class="fa fa-minus-circle"></i></a>
								<div class="modal fade" id="modal_<?=$v->id?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title" id="exampleModalLabel"><?=$v->name?></h4>
											</div>
											<div class="modal-body">Confirmer la suppression de la team : <?=$v->name?></div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
												<button onclick="window.location.href='/team/del/<?=$v->id?>?management&gaming=true'" type="button" class="btn btn-primary">Supprimer</button>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php
					endforeach;
					?>
					</tbody>
				</table>  
			</div>
		</div>
	</div>
</div>