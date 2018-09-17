<?php

// FONCTIONS DE CONNEXION

function connect()
{ 
	$hote="localhost";
	$login="festival";
	$mdp="secret";
	$dns="mysql:host=;dbname=$login";
	$connexion= new PDO($dns, $login, $mdp);
	return $connexion;
}

// FONCTIONS DE GESTION DES ÉTABLISSEMENTS

function obtenirReqEtablissements()
{
	$req="
		select id, nom 
		from Etablissement 
		order by id
	";
	return $req;
}

function obtenirReqEtablissementsOffrantChambres()
{
	$req="
		select id, nom, nombreChambresOffertes 
		from Etablissement 
		where nombreChambresOffertes!=0 
		order by id
	";
	return $req;
}

function obtenirReqEtablissementsAyantChambresAttribuées()
{
	$req="
		select distinct id, nom, nombreChambresOffertes 
		from Etablissement, Attribution 
		where id = idEtab 
		order by id
	";
	return $req;
}

function obtenirDetailEtablissement($connexion, $id)
{
	$req="
		select * 
		from Etablissement 
		where id='$id'
	";
   
   $rsEtab=$req->execute();
   return $rsEtab->fetch(PDO::FETCH_ASSOC);
}

function supprimerEtablissement($connexion, $id)
{
	$req="
		delete *
		from Etablissement 
		where id='$id'
	";
	$connexion->prepare($req);
	$req->execute();
}
 
function modifierEtablissement($connexion, $id, $nom, $adresseRue, $codePostal, 
                               $ville, $tel, $adresseElectronique, $type, 
                               $civiliteResponsable, $nomResponsable, 
                               $prenomResponsable, $nombreChambresOffertes)
{  
   $nom=str_replace("'", "''", $nom);
   $adresseRue=str_replace("'","''", $adresseRue);
   $ville=str_replace("'","''", $ville);
   $adresseElectronique=str_replace("'","''", $adresseElectronique);
   $nomResponsable=str_replace("'","''", $nomResponsable);
   $prenomResponsable=str_replace("'","''", $prenomResponsable);
  
   $req="update Etablissement set nom='$nom',adresseRue='$adresseRue',
         codePostal='$codePostal',ville='$ville',tel='$tel',
         adresseElectronique='$adresseElectronique',type='$type',
         civiliteResponsable='$civiliteResponsable',nomResponsable=
         '$nomResponsable',prenomResponsable='$prenomResponsable',
         nombreChambresOffertes='$nombreChambresOffertes' where id='$id'";
   
    $connexion->prepare($req);
	$req->execute();
}

function creerEtablissement($connexion, $id, $nom, $adresseRue, $codePostal, 
                            $ville, $tel, $adresseElectronique, $type, 
                            $civiliteResponsable, $nomResponsable, 
                            $prenomResponsable, $nombreChambresOffertes)
{ 
   $nom=str_replace("'", "''", $nom);
   $adresseRue=str_replace("'","''", $adresseRue);
   $ville=str_replace("'","''", $ville);
   $adresseElectronique=str_replace("'","''", $adresseElectronique);
   $nomResponsable=str_replace("'","''", $nomResponsable);
   $prenomResponsable=str_replace("'","''", $prenomResponsable);
   
   $req="insert into Etablissement values ('$id', '$nom', '$adresseRue', 
         '$codePostal', '$ville', '$tel', '$adresseElectronique', '$type', 
         '$civiliteResponsable', '$nomResponsable', '$prenomResponsable',
         '$nombreChambresOffertes')";
   
	$connexion->prepare($req);
	$req->execute();
}


function estUnIdEtablissement($connexion, $id)
{
   $req=$connexion->prepare("
							select * 
							from Etablissement 
							where id='$id'
							");
   $rsEtab=$req->execute();
   return $rsEtab->fetch(PDO::FETCH_ASSOC);
}

function estUnNomEtablissement($connexion, $mode, $id, $nom)
{
   $nom=str_replace("'", "''", $nom);
   // S'il s'agit d'une création, on vérifie juste la non existence du nom sinon
   // on vérifie la non existence d'un autre établissement (id!='$id') portant 
   // le même nom
   if ($mode=='C')
   {
	  $req=$connexion->prepare("
							select * 
							from Etablissement 
							where nom='$nom'
							");
   }
   else
   {
	  $req=$connexion->prepare("
							select * 
							from Etablissement 
							where nom='$nom' 
							and id!='$id'
							");
   }
   $rsEtab=$req->execute();
   return $rsEtab->fetch(PDO::FETCH_ASSOC);
}

function obtenirNbEtab($connexion)
{
   $req=$connexion->prepare("
							select count(*) as nombreEtab 
							from Etablissement
							");
   $rsEtab=$req->execute();
   $lgEtab=$req->fetch(PDO::FETCH_ASSOC);
   return $lgEtab["nombreEtab"];
}

function obtenirNbEtabOffrantChambres($connexion)
{
	$req=$connexion->prepare("
							select count(*) as nombreEtabOffrantChambres 
							from Etablissement 
							where nombreChambresOffertes!=0
							");
	$rsEtabOffrantChambres=$req->execute();
	$lgEtabOffrantChambres=$req->fetch(PDO::FETCH_ASSOC);
	return $lgEtabOffrantChambres["nombreEtabOffrantChambres"];
}

// Retourne false si le nombre de chambres transmis est inférieur au nombre de 
// chambres occupées pour l'établissement transmis 
// Retourne true dans le cas contraire
function estModifOffreCorrecte($connexion, $idEtab, $nombreChambres)
{
   $nbOccup=obtenirNbOccup($connexion, $idEtab);
   return ($nombreChambres>=$nbOccup);
}

// FONCTIONS RELATIVES AUX GROUPES

function obtenirReqIdNomGroupesAHeberger()
{
	$req=$connexion->prepare("
							select id, nom 
							from Groupe 
							where hebergement='O' 
							order by id
							";
	return $req;
}

function obtenirNomGroupe($connexion, $id)
{
	$req=$connexion->prepare("
							select nom 
							from Groupe 
							where id='$id'
							";
	$rsGroupe=$req->execute();
	$lgGroupe=$req->fetch(PDO::FETCH_ASSOC);
	return $lgGroupe["nom"];
}

// FONCTIONS RELATIVES AUX ATTRIBUTIONS

// Teste la présence d'attributions pour l'établissement transmis    
function existeAttributionsEtab($connexion, $id)
{
   $req=$connexion->prepare("
							select * 
							From Attribution 
							where idEtab='$id'
							";
   $rsAttrib=$req->execute();
   return $req->fetch(PDO::FETCH_ASSOC);
}

// Retourne le nombre de chambres occupées pour l'id étab transmis
function obtenirNbOccup($connexion, $idEtab)
{
   $req=$connexion->prepare("
						   select IFNULL(sum(nombreChambres), 0) as totalChambresOccup 
						   from Attribution 
						   where idEtab='$idEtab'
						   ";
   $rsOccup=$req->execute();
   $lgOccup=$req->fetch(PDO::FETCH_ASSOC);
   return $lgOccup["totalChambresOccup"];
}

// Met à jour (suppression, modification ou ajout) l'attribution correspondant à
// l'id étab et à l'id groupe transmis
function modifierAttribChamb($connexion, $idEtab, $idGroupe, $nbChambres)
{
   $req=$connexion->prepare("
						   select count(*) as nombreAttribGroupe 
						   from Attribution where idEtab='$idEtab' 
						   and idGroupe='$idGroupe'
						   ";
   $rsAttrib=$req->execute();
   $lgAttrib=$req->fetch(PDO::FETCH_ASSOC);
   if ($nbChambres==0)
      $req="delete from Attribution where idEtab='$idEtab' and idGroupe='$idGroupe'";
   else
   {
      if ($lgAttrib["nombreAttribGroupe"]!=0)
         $req="update Attribution set nombreChambres=$nbChambres where idEtab=
              '$idEtab' and idGroupe='$idGroupe'";
      else
         $req="insert into Attribution values('$idEtab','$idGroupe', $nbChambres)";
   }
   mysql_query($req, $connexion);
}

// Retourne la requête permettant d'obtenir les id et noms des groupes affectés
// dans l'établissement transmis
function obtenirReqGroupesEtab($id)
{
   $req="select distinct id, nom from Groupe, Attribution where 
        Attribution.idGroupe=Groupe.id and idEtab='$id'";
   return $req;
}
            
// Retourne le nombre de chambres occupées par le groupe transmis pour l'id étab
// et l'id groupe transmis
function obtenirNbOccupGroupe($connexion, $idEtab, $idGroupe)
{
   $req="select nombreChambres From Attribution where idEtab='$idEtab'
        and idGroupe='$idGroupe'";
   $rsAttribGroupe=mysql_query($req, $connexion);
   if ($lgAttribGroupe=mysql_fetch_array($rsAttribGroupe))
      return $lgAttribGroupe["nombreChambres"];
   else
      return 0;
}

?>

