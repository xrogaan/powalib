<?php
    require_once 'header.php';

    /*  ###########################################################################

        On commence fort :
        Un formulaire utilisant la session pour sauvegarder ses valeurs, avec une pr�sentation en template, et du javascript

        ###########################################################################
    */
    // on cr�� un form
    $action_form = new FormBase(    'demo_select_action',       // son nom est 'demo_select_action',
                                    url_from_get_vars( false ), // son url de submit est l'url du script courant (url_from_get_vars)
                                    false,                      // pr�fixe de cl� par d�faut
                                    array(                      // liste des champs du form
                                        new FormFieldDomain(    // on lui met un seul champ  qui est un s�lecteur :
                                            'action',               // nom du champ
                                            true,                   // obligtoire
                                            false,                  // pas de validation (la v�rification que la valeur est bien dans la liste du s�lecteur est automatique)
                                            "Choisir une d�mo :",   // Nom du champ
                                            null,                   // pas de valeur par d�faut
                                            '',                     // texte d'aide
                                            array(                  // valeurs du select
                                                1   =>  "Formulaire simple",
                                                2   =>  "Formulaire g�n�r�",
                                                7   =>  "Formulaire g�n�r� ++",
                                                3   =>  "Tous les types de champs texte",
                                                4   =>  "Tous les types de s�lecteurs",
                                                5   =>  "Upload",
                                                6   =>  "Personnel",
                                                7   =>  "Dynamic",
                                                8   =>  "Voir le code source de cette page",
                                                )
                                            )
                                        )
                                );

    // on ajoute un peu de javascript sur le select
    $action_form->action->SetDisplayMode( array( 'options' => 'onchange="document.forms.demo_select_action.submit();"' ));

    // On submit en utilisant la session pour m�moriser la valeur
    $action_form_submit = $action_form->SubmitFormUsingSession();

    // On r�cup�re la valeur du select (qui sera NULL si le form n'a pas �t� submit)
    $action = $action_form->action->value;

    // Code qui ne sert � rien, c'est juste pour montrer les diff�rentes valeurs de retour de SubmitFormUsingSession()
    switch( $action_form_submit )
    {
        case 'post':
            $sub = "Choix enregistr�.";
            break;

        case 'session':
            $sub ="Valeur prise dans la session.";
            break;

        default:
            $sub ="Aucune valeur � utiliser.";
    }

    // On affiche toujours le form, avec un template
    $d = $action_form->RenderFormTemplate();
?>
    <?php echo $d['_formtag'] ?>
    <table>
        <tr>
            <td><b><?php echo $d['action']['name'] ?></b></td>
            <td><b><?php echo $d['action']['input'] ?></b></td>
            <td><b><?php echo $d['action']['error'] ?></b></td>
            <td><b><?php echo $d['action']['help'] ?></b></td>
            <td><input type="submit"></td>
            <td><?php echo "$sub [$action][$action_form_submit]" ?></td>
        </tr>
    </table>
    </form>
<?php

    switch( $action )
    {
        /*  ###########################################################################

            Form simple

            ###########################################################################
        */
        case 1:
            ?>
            <h1>Un exemple de formulaire simple, avec quelques champs de base.</h1>
            <?php
            $form = new FormBase(   'example',                  // son nom
                                    url_from_get_vars( false ), // son url de submit est l'url du script courant (url_from_get_vars)
                                    false,                      // pr�fixe de cl� par d�faut
                                    array(                      // liste des champs du form

                                        "Informations Utilisateur", // Titre du chapitre 1

                                        new FormFieldString(        // on lui met un champ  de texte
                                            'prenom',               // nom du champ
                                            true,                   // obligtoire
                                            new FormValidatorString( 1,30 ),    // longueur min et max
                                            "Pr�nom",               // Nom du champ
                                            null,                   // pas de valeur par d�faut
                                            ''                      // texte d'aide
                                            ),

                                        new FormFieldString( 'nom', true, new FormValidatorString( 1,30 ), "Nom", null, '' ),
                                        new FormFieldString( 'email', true, new FormValidatorPerlRegexp(
                                                                    1,50, '/^[a-z\-_]+(\.[a-z\-_]+)*@[a-z\-_]+(\.[a-z\-_]+)$/', 'e-mail invalide' ),
                                                                "Adresse email", null, 'Utilise une expression r�guli�re PCRE pour la validation. Juste un exemple, pour valider r�ellement un email, il faut une regexp plus complexe.' ),

                                        "Autres informations",      // Titre du chapitre 2

                                        new FormFieldDomain( 'food1', true, false, "Tu pr�f�res...", null, '', array( "Les frites", "Les nouilles", "Les courges", "Rien" )),
                                        new FormFieldRadio( 'food2', true, false, "Tu hais...", null, '', array( "Les frites", "Les nouilles", "Les courges", "Rien" )),
                                        new FormFieldYesNo( 'read', false, false, "Tu sais lire", null, '' ),
                                    )
                                );

            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a �t� valid�
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // exemple (pas bien)
                echo "Ton nom est : ".htmlentities($form->prenom->value)." ".htmlentities($form->nom->value).' et tu aimes : '.$form->food1->domain[$form->food1->value].'<br>';

                // Exemple (bien car g�re le quoting HTML tout seul
                echo "Ton nom est : ".$form->prenom->DisplayValue()." ".$form->nom->DisplayValue().' et tu aimes : '.$form->food1->DisplayValue().'<br><br>';

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour exp�rimentation
                $form->DisplayForm();
            }
            break;

        /*  ###########################################################################

            Form g�n�r�

            ###########################################################################
        */
        case 2:
            ?>
            <h1>Un exemple de formulaire g�n�r� automatiquement.</h1>
            <?php

            // On g�n�re un form sauv� dans la session pour demander le nombre de questions du sondage...
            $number_form = new FormBase( 'sondage_nb_questions', url_from_get_vars( false ), false, array(
                new FormFieldString( 'number', true, new FormValidatorInteger( 2,10 ), "Nombre de questions", 5, '' ) ) );
            $number_form->SubmitFormUsingSession();
            $number_form->DisplayForm();
            $questions = $number_form->number->value;

            if( $questions )
            {
                // Cr�� une liste de champs de base
                $fields = array( "Sondage" );

                // Ajoute des champs (premier exemple)
                foreach( array( 'nom' => "Nom du sondage", 'question' => "Question pos�e" ) as $key => $name )
                    $fields[] = new FormFieldString( $key, true, new FormValidatorString( 1,50 ), $name, null, '' );

                $fields[] = "R�ponses";

                // Ajoute des champs (second exemple)
                for( $i=1; $i<=$questions; $i++ )
                    $fields[] = new FormFieldString( "reponse_$i", true, new FormValidatorString( 1,50 ), "R�ponse $i", null, '' );

                // Cr�� le form
                $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

                if( $form->Process() )
                {
                    // Si on arrive ici, c'est que tout a �t� valid�
                    ?><h3>Informations extraites du formulaire :</h3><?php

                    // Affiche les informations brutes
                    xdump( $form->ExtractValues() );

                    // Affiche les informations, joli
                    $form->DisplayHTML();

                    /*  exemple de cr�ation de requ�te SQL
                        note: on n'utilise pas �a en g�n�ral, car DBObject est bien plus puissant
                    */
                    list($fields, $values, $percents) = $form->ExtractValuesSQL();
                    $sql = "INSERT INTO sondages (".implode(', ',$fields).") VALUES (".implode(', ',$percents).")";
                    echo "<br>Exemple SQL :<br>".prettyq($sql)."<br>Escaping automatique des param�tres :<br><br>";

                    echo prettyq( db_quote_query($sql, $values));

                    // remet le formulaire pour exp�rimentation
                    $form->DisplayForm();
                }
            }
            break;

        /*  ###########################################################################

            Form g�n�r� avec bouton pour ajouter et supprimer des champs

            ###########################################################################
        */
        case 7:
            ?>
            <h1>Un exemple de formulaire g�n�r� automatiquement, avec ajout et suppression de champs.</h1>
            <?php

            // Cr�� une liste de champs de base
            $fields = array( "Sondage",
                new FormFieldHidden( 'reponses', false, false, 5 ),
                new FormFieldHidden( 'addreponse', false, false, 0 ),
                new FormFieldHidden( 'packreponses', false, false, 0 ),
            );

            // Cr�� un form bidon avec le m�me nom que le form normal, pour extraire le nombre de reponses du form actuel
            // on a besoin de �a, car �a influe sur le nombre de champs du form.
            $form = new FormBase( 'example', url_from_get_vars( false ), false, $fields );
            if( $form->SubmitForm() )
            {
                $reponses = $form->reponses->value + $form->addreponse->value;
                $packreponses = $form->packreponses->value;
            }
            else
            {
                $reponses = 5;
                $packreponses = false;
            }

            // si on a fait "ajouter une reponse", il ne faut pas submitter le form, mais le r�afficher
            $redisplay = $form->addreponse->value || $form->packreponses->value;

            // Ajoute des champs (premier exemple)
            foreach( array( 'nom' => "Nom du sondage", 'question' => "Question pos�e" ) as $key => $name )
                $fields[] = new FormFieldString( $key, true, new FormValidatorString( 1,50 ), $name, null, '' );

            $fields[] = "R�ponses";

            // Ajoute des champs (second exemple)
            for( $i=1; $i<=$reponses; $i++ )
                $fields[] = new FormFieldString( "reponse_$i", true, new FormValidatorString( 1,50 ), "R�ponse $i", null, '' );

            // Cr�� le form
            $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

            $addreponse = '
    <div class=clickable onclick="document.forms.example.example_addreponse.value=1;document.forms.example.submit();">Ajouter une reponse</div>
    <div class=clickable onclick="document.forms.example.example_addreponse.value=-1;document.forms.example.submit();">Supprimer une reponse</div>
    <div class=clickable onclick="document.forms.example.example_packreponses.value=1;document.forms.example.submit();">Supprimer les lignes vides</div>
    ';
            if( $redisplay )
            {
                echo 'redisplay<br>';
                $form->SubmitForm();    // submit le formulaire et enregistre les donn�es du POST dedans
                $form->reponses->value      = $reponses;    // modifie les valeurs
                $form->addreponse->value    = 0;
                $form->packreponses->value  = 0;

                // compacter les reponses
                if( $packreponses )
                {
                    $p = 1;
                    for( $i=1; $i<=$reponses; $i++ )
                    {
                        $k  = "reponse_$i";
                        $nk = "reponse_$p";
                        $v  = $form->$k->form_value;
                        $form->$k->form_value = $form->$k->value = null;
                        if( $v !== '' )
                        {
                            $form->$nk->form_value = $v;
                            $p++;
                        }
                    }
                }

                $form->DisplayForm();   // affiche avec les nouvelles valeurs
                echo $addreponse;
            }
            elseif( !$form->Process() )
            {
                echo $addreponse;       // si la validation a �chou�, on affiche tout de m�me les liens pour ajouter et enlever des reponses
            }
            else
            {
                // Si on arrive ici, c'est que tout a �t� valid�
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                /*  exemple de cr�ation de requ�te SQL
                    note: on n'utilise pas �a en g�n�ral, car DBObject est bien plus puissant
                */
                list($fields, $values, $percents) = $form->ExtractValuesSQL();
                $sql = "INSERT INTO sondages (".implode(', ',$fields).") VALUES (".implode(', ',$percents).")";
                echo "<br>Exemple SQL :<br>".prettyq($sql)."<br>Escaping automatique des param�tres :<br><br>";
                echo prettyq( db_quote_query( $sql, $values ));

                // remet le formulaire pour exp�rimentation
                $form->DisplayForm();
            }
            break;

        /*  ###########################################################################

            Tous les types de champs

            ###########################################################################
        */
        case 3:
            ?>
            <h1>Tous les types de champs</h1>
            <?php

            // Cr�� une liste de champs de base
            $fields = array(
                new FormFieldString( 'string', true, new FormValidatorString( 1,50 ), 'FormFieldString', null, 'Champ texte de base' ),

                // exemple de validation par regexp perl (PCRE)
                new FormFieldString( 'string_lower', false, new FormValidatorPerlRegexp( 1,50,'/^[a-z_]+$/','a-z et _ uniquement' ), 'Texte en minuscules', null, 'Champ texte de base, avec expression r�guli�re PCRE de validation autorisant uniquement a-z et _' ),

                // Exemples de nombres
                new FormFieldString( 'myinteger', true, new FormValidatorInteger( 1,1000 ), 'Entier', null, 'Un entier entre 1 et 1000' ),
                new FormFieldString( 'myfloat', true, new FormValidatorFloat( 1,1000 ), 'Entier', null, 'Un nombre flottant entre 1.0 et 1000.0' ),
                new FormFieldString( 'ipaddress', true, new FormValidatorIPAddress( ), 'Adresse IP', null, 'Une adresse IP' ),


                // textarea sans options
                new FormFieldTextArea( 'textarea1', true, new FormValidatorString( 1,50 ), 'FormFieldTextArea', null, 'Textarea' ),

                // textarea avec options
                new FormFieldTextArea( 'textarea2', true, new FormValidatorString( 1,50 ), 'FormFieldTextArea', null, 'Textarea + options (1)', 'rows=5 cols=20' ),

                // autres types
                new FormFieldPassword( 'password', true, new FormValidatorString( 1,50 ), 'FormFieldPassword', null, 'Password ******' ),

                new FormFieldHidden( 'hidden', false, false, 'invisible' ),

                new FormFieldDate( 'datema', false, false, 'FormFieldDate', null, 'Date, need_days=false', false ),
                new FormFieldDate( 'datejma', false, false, 'FormFieldDate', null, 'Date, need_days=true', true ),
                new FormFieldTime( 'time', false, false, 'FormFieldTime', null, 'Heure' ),

                // Validateur composite : comparaison de deux champs (entier)
                new FormFieldString( 'minimum', true, new FormValidatorInteger( 1,1000 ), 'Valeur minimum', null, '' ),
                new FormFieldString( 'maximum', true,
                    new FormValidatorComposite(         // ex�cute les 2 validateurs ci-dessous dans l'ordre
                        array(
                            new FormValidatorInteger( 1,1000 ),         // d'abord, v�rifier que c'est un entier, et convertir en entier
                            new FormValidatorGreaterThan( 'minimum' )   // on donne le nom du champ qui doit �tre utilis� pour la comparaison
                        )),
                    'Valeur maximum', null, 'On teste que c\'est sup�rieur au minimum' ),

                // Validateur composite : comparaison de deux champs (date) (avec champ facultatif)
                new FormFieldDate( 'minimum_date', false, false, 'Date de d�but', null, '', true ), // le validateur de date par d�faut est utilis�
                new FormFieldDate( 'maximum_date', false,
                    new FormValidatorComposite(         // ex�cute les 2 validateurs ci-dessous dans l'ordre. on doit remettre un FormValidatorDate car on �crase le validateur par d�faut
                        array(
                            new FormValidatorDate(),
                            new FormValidatorGreaterThan( 'minimum_date', 'date' ), // on donne le nom du champ qui doit �tre utilis� pour la comparaison, avec la r�gle 'date'
                        )),
                    'Date de fin', null, 'On teste que c\'est sup�rieur � la date de d�but', true ),


            );

            // textarea avec options (autre fa�on de faire)
            $field = new FormFieldTextArea( 'textarea3', true, new FormValidatorString( 1,50 ), 'FormFieldTextArea', null, 'Textarea + options (2)' );
            $field->SetDisplayMode( array( 'options'=>'rows=4 cols=25', 'mode'=>'textarea' ));
            $fields[] = $field;

            // Cr�� le form
            $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a �t� valid�
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour exp�rimentation
                $form->DisplayForm();
            }
            break;

        /*  ###########################################################################

            Tous les types de s�lecteurs

            ###########################################################################
        */
        case 4:
            ?>
            <h1>Tous les types de champs</h1>
            <?php

            $domain = array( 1 => "Oui", 2=>"Non", 3=>"Peut-�tre", 4=>"Je ne sais pas lire" );

            // Cr�� une liste de champs de base
            $fields = array(
                new FormFieldBoolean( 'bool', false, false, 'FormFieldBoolean', null, 'Bool�en Checkbox (ne peut pas �tre obligatoire)' ),
                new FormFieldYesNo( 'yesno', true, false, 'FormFieldYesNo', null, 'Bool�en oui/non (peut �tre obligatoire)' ),

            // diff�rents types de s�lecteurs
            // les cl�s peuvent �tre des nombres (incluant 0) ou des strings
                new FormFieldDomain( 'selector', false, false, 'FormFieldDomain', null, "S�lecteur standard", $domain ),
                new FormFieldRadio( 'radio', false, false, 'FormFieldRadio', null, "S�lecteur � boutons radio", $domain ),
                new FormFieldMultiple( 'checkboxes', false, false, 'FormFieldMultiple', null, "S�lecteur multiple � checkbox", $domain ),
            );

            // Cr�� le form
            $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a �t� valid�
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour exp�rimentation
                $form->DisplayForm();
            }
            break;

        /*  ###########################################################################

            Upload de fichiers

            ###########################################################################
        */
        case 5:
            ?>
            <h1>Upload</h1>
            <?php

            // Cr�� une liste de champs de base
            $fields = array(
                new FormFieldUpload( 'file', true,
                    new FormValidatorUpload(
                        array(
                            'content_types' => array( 'regex' => '#^image/.*$#' ),
                            'max_size'      => 512*1024,    // en octets
                            'image'         => array(
                                'content_types' => array( 'regex' => '#^image/(png|jpeg|jpg)$#' ),
                                'width_max'     => 800,     // en pixels
                                'height_max'    => 600,
                            ),
                    )),
                    'Fichier � envoyer (image jpeg ou png, 800x600 max, 512k max)', null, '' ),
                new FormFieldString( 'filename', true, new FormValidatorPerlRegexp( 1,50, '/^[a-z0-9_]+\.[a-z0-9]+$/', "Le nom et l'extension doivent se composer uniquement de minuscules et de chiffres" ), 'Nom � donner � ce fichier sur le serveur', null, 'Le fichier ne sera pas sera sauv�.' ),
            );

            // Cr�� le form
            $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a �t� valid�
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour exp�rimentation
                $form->DisplayForm();
            }
            break;
        
        case 6:
            $u = array (
              'id' => '1',
              'pseudo' => 'annonymous',
              'password' => '',
              'email' => 'root@mail.com',
              'last_seen' => '1153046154',
              'registered' => '1150799299',
              'active' => '1',
              'active_key' => '1150799299',
              'icingdeath' => '0',
              'gid' => '4',
            );

            $domain = array (
              1 => 'Administrateurs',
              2 => 'Moderateurs',
              3 => 'Membres',
              4 => 'Annonymes',
            );
            
            $gu = array (
              1 => 4,
            );
            
            $fields = array(
                new FormFieldString( 'pseudo', true,new FormValidatorString(5,250),'Pseudo :',$u['pseudo'],'' ),
                new FormFieldPassword('password',true,new FormValidatorString(6,20),'Password :',$u['password'],''),
                new FormFieldString('email',true,new FormValidatorString(5,250),'E-mail :',$u['email'],''),
                new FormFieldRadio('actif',true,false,'Actif :',$u['active'],'',array(0=>'Incatif',1=>'Actif')),
                new FormFieldMultiple('groupe',false,false,'Groupe :',$gu,'User groups',$domain ),
            );
            
            $form = new FormBase(   'user_edit', url_from_get_vars( false ), false, $fields );
            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a �t� valid�
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour exp�rimentation
                $form->DisplayForm();
            }
            break;
        
        case 8:
            highlight_file( __FILE__ );
    }


