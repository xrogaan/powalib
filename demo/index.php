<?php
    require_once 'header.php';

    /*  ###########################################################################

        On commence fort :
        Un formulaire utilisant la session pour sauvegarder ses valeurs, avec une présentation en template, et du javascript

        ###########################################################################
    */
    // on créé un form
    $action_form = new FormBase(    'demo_select_action',       // son nom est 'demo_select_action',
                                    url_from_get_vars( false ), // son url de submit est l'url du script courant (url_from_get_vars)
                                    false,                      // préfixe de clé par défaut
                                    array(                      // liste des champs du form
                                        new FormFieldDomain(    // on lui met un seul champ  qui est un sélecteur :
                                            'action',               // nom du champ
                                            true,                   // obligtoire
                                            false,                  // pas de validation (la vérification que la valeur est bien dans la liste du sélecteur est automatique)
                                            "Choisir une démo :",   // Nom du champ
                                            null,                   // pas de valeur par défaut
                                            '',                     // texte d'aide
                                            array(                  // valeurs du select
                                                1   =>  "Formulaire simple",
                                                2   =>  "Formulaire généré",
                                                7   =>  "Formulaire généré ++",
                                                3   =>  "Tous les types de champs texte",
                                                4   =>  "Tous les types de sélecteurs",
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

    // On submit en utilisant la session pour mémoriser la valeur
    $action_form_submit = $action_form->SubmitFormUsingSession();

    // On récupère la valeur du select (qui sera NULL si le form n'a pas été submit)
    $action = $action_form->action->value;

    // Code qui ne sert à rien, c'est juste pour montrer les différentes valeurs de retour de SubmitFormUsingSession()
    switch( $action_form_submit )
    {
        case 'post':
            $sub = "Choix enregistré.";
            break;

        case 'session':
            $sub ="Valeur prise dans la session.";
            break;

        default:
            $sub ="Aucune valeur à utiliser.";
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
                                    false,                      // préfixe de clé par défaut
                                    array(                      // liste des champs du form

                                        "Informations Utilisateur", // Titre du chapitre 1

                                        new FormFieldString(        // on lui met un champ  de texte
                                            'prenom',               // nom du champ
                                            true,                   // obligtoire
                                            new FormValidatorString( 1,30 ),    // longueur min et max
                                            "Prénom",               // Nom du champ
                                            null,                   // pas de valeur par défaut
                                            ''                      // texte d'aide
                                            ),

                                        new FormFieldString( 'nom', true, new FormValidatorString( 1,30 ), "Nom", null, '' ),
                                        new FormFieldString( 'email', true, new FormValidatorPerlRegexp(
                                                                    1,50, '/^[a-z\-_]+(\.[a-z\-_]+)*@[a-z\-_]+(\.[a-z\-_]+)$/', 'e-mail invalide' ),
                                                                "Adresse email", null, 'Utilise une expression régulière PCRE pour la validation. Juste un exemple, pour valider réellement un email, il faut une regexp plus complexe.' ),

                                        "Autres informations",      // Titre du chapitre 2

                                        new FormFieldDomain( 'food1', true, false, "Tu préfères...", null, '', array( "Les frites", "Les nouilles", "Les courges", "Rien" )),
                                        new FormFieldRadio( 'food2', true, false, "Tu hais...", null, '', array( "Les frites", "Les nouilles", "Les courges", "Rien" )),
                                        new FormFieldYesNo( 'read', false, false, "Tu sais lire", null, '' ),
                                    )
                                );

            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a été validé
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // exemple (pas bien)
                echo "Ton nom est : ".htmlentities($form->prenom->value)." ".htmlentities($form->nom->value).' et tu aimes : '.$form->food1->domain[$form->food1->value].'<br>';

                // Exemple (bien car gère le quoting HTML tout seul
                echo "Ton nom est : ".$form->prenom->DisplayValue()." ".$form->nom->DisplayValue().' et tu aimes : '.$form->food1->DisplayValue().'<br><br>';

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour expérimentation
                $form->DisplayForm();
            }
            break;

        /*  ###########################################################################

            Form généré

            ###########################################################################
        */
        case 2:
            ?>
            <h1>Un exemple de formulaire généré automatiquement.</h1>
            <?php

            // On génère un form sauvé dans la session pour demander le nombre de questions du sondage...
            $number_form = new FormBase( 'sondage_nb_questions', url_from_get_vars( false ), false, array(
                new FormFieldString( 'number', true, new FormValidatorInteger( 2,10 ), "Nombre de questions", 5, '' ) ) );
            $number_form->SubmitFormUsingSession();
            $number_form->DisplayForm();
            $questions = $number_form->number->value;

            if( $questions )
            {
                // Créé une liste de champs de base
                $fields = array( "Sondage" );

                // Ajoute des champs (premier exemple)
                foreach( array( 'nom' => "Nom du sondage", 'question' => "Question posée" ) as $key => $name )
                    $fields[] = new FormFieldString( $key, true, new FormValidatorString( 1,50 ), $name, null, '' );

                $fields[] = "Réponses";

                // Ajoute des champs (second exemple)
                for( $i=1; $i<=$questions; $i++ )
                    $fields[] = new FormFieldString( "reponse_$i", true, new FormValidatorString( 1,50 ), "Réponse $i", null, '' );

                // Créé le form
                $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

                if( $form->Process() )
                {
                    // Si on arrive ici, c'est que tout a été validé
                    ?><h3>Informations extraites du formulaire :</h3><?php

                    // Affiche les informations brutes
                    xdump( $form->ExtractValues() );

                    // Affiche les informations, joli
                    $form->DisplayHTML();

                    /*  exemple de création de requête SQL
                        note: on n'utilise pas ça en général, car DBObject est bien plus puissant
                    */
                    list($fields, $values, $percents) = $form->ExtractValuesSQL();
                    $sql = "INSERT INTO sondages (".implode(', ',$fields).") VALUES (".implode(', ',$percents).")";
                    echo "<br>Exemple SQL :<br>".prettyq($sql)."<br>Escaping automatique des paramètres :<br><br>";

                    echo prettyq( db_quote_query($sql, $values));

                    // remet le formulaire pour expérimentation
                    $form->DisplayForm();
                }
            }
            break;

        /*  ###########################################################################

            Form généré avec bouton pour ajouter et supprimer des champs

            ###########################################################################
        */
        case 7:
            ?>
            <h1>Un exemple de formulaire généré automatiquement, avec ajout et suppression de champs.</h1>
            <?php

            // Créé une liste de champs de base
            $fields = array( "Sondage",
                new FormFieldHidden( 'reponses', false, false, 5 ),
                new FormFieldHidden( 'addreponse', false, false, 0 ),
                new FormFieldHidden( 'packreponses', false, false, 0 ),
            );

            // Créé un form bidon avec le même nom que le form normal, pour extraire le nombre de reponses du form actuel
            // on a besoin de ça, car ça influe sur le nombre de champs du form.
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

            // si on a fait "ajouter une reponse", il ne faut pas submitter le form, mais le réafficher
            $redisplay = $form->addreponse->value || $form->packreponses->value;

            // Ajoute des champs (premier exemple)
            foreach( array( 'nom' => "Nom du sondage", 'question' => "Question posée" ) as $key => $name )
                $fields[] = new FormFieldString( $key, true, new FormValidatorString( 1,50 ), $name, null, '' );

            $fields[] = "Réponses";

            // Ajoute des champs (second exemple)
            for( $i=1; $i<=$reponses; $i++ )
                $fields[] = new FormFieldString( "reponse_$i", true, new FormValidatorString( 1,50 ), "Réponse $i", null, '' );

            // Créé le form
            $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

            $addreponse = '
    <div class=clickable onclick="document.forms.example.example_addreponse.value=1;document.forms.example.submit();">Ajouter une reponse</div>
    <div class=clickable onclick="document.forms.example.example_addreponse.value=-1;document.forms.example.submit();">Supprimer une reponse</div>
    <div class=clickable onclick="document.forms.example.example_packreponses.value=1;document.forms.example.submit();">Supprimer les lignes vides</div>
    ';
            if( $redisplay )
            {
                echo 'redisplay<br>';
                $form->SubmitForm();    // submit le formulaire et enregistre les données du POST dedans
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
                echo $addreponse;       // si la validation a échoué, on affiche tout de même les liens pour ajouter et enlever des reponses
            }
            else
            {
                // Si on arrive ici, c'est que tout a été validé
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                /*  exemple de création de requête SQL
                    note: on n'utilise pas ça en général, car DBObject est bien plus puissant
                */
                list($fields, $values, $percents) = $form->ExtractValuesSQL();
                $sql = "INSERT INTO sondages (".implode(', ',$fields).") VALUES (".implode(', ',$percents).")";
                echo "<br>Exemple SQL :<br>".prettyq($sql)."<br>Escaping automatique des paramètres :<br><br>";
                echo prettyq( db_quote_query( $sql, $values ));

                // remet le formulaire pour expérimentation
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

            // Créé une liste de champs de base
            $fields = array(
                new FormFieldString( 'string', true, new FormValidatorString( 1,50 ), 'FormFieldString', null, 'Champ texte de base' ),

                // exemple de validation par regexp perl (PCRE)
                new FormFieldString( 'string_lower', false, new FormValidatorPerlRegexp( 1,50,'/^[a-z_]+$/','a-z et _ uniquement' ), 'Texte en minuscules', null, 'Champ texte de base, avec expression régulière PCRE de validation autorisant uniquement a-z et _' ),

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
                    new FormValidatorComposite(         // exécute les 2 validateurs ci-dessous dans l'ordre
                        array(
                            new FormValidatorInteger( 1,1000 ),         // d'abord, vérifier que c'est un entier, et convertir en entier
                            new FormValidatorGreaterThan( 'minimum' )   // on donne le nom du champ qui doit être utilisé pour la comparaison
                        )),
                    'Valeur maximum', null, 'On teste que c\'est supérieur au minimum' ),

                // Validateur composite : comparaison de deux champs (date) (avec champ facultatif)
                new FormFieldDate( 'minimum_date', false, false, 'Date de début', null, '', true ), // le validateur de date par défaut est utilisé
                new FormFieldDate( 'maximum_date', false,
                    new FormValidatorComposite(         // exécute les 2 validateurs ci-dessous dans l'ordre. on doit remettre un FormValidatorDate car on écrase le validateur par défaut
                        array(
                            new FormValidatorDate(),
                            new FormValidatorGreaterThan( 'minimum_date', 'date' ), // on donne le nom du champ qui doit être utilisé pour la comparaison, avec la règle 'date'
                        )),
                    'Date de fin', null, 'On teste que c\'est supérieur à la date de début', true ),


            );

            // textarea avec options (autre façon de faire)
            $field = new FormFieldTextArea( 'textarea3', true, new FormValidatorString( 1,50 ), 'FormFieldTextArea', null, 'Textarea + options (2)' );
            $field->SetDisplayMode( array( 'options'=>'rows=4 cols=25', 'mode'=>'textarea' ));
            $fields[] = $field;

            // Créé le form
            $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a été validé
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour expérimentation
                $form->DisplayForm();
            }
            break;

        /*  ###########################################################################

            Tous les types de sélecteurs

            ###########################################################################
        */
        case 4:
            ?>
            <h1>Tous les types de champs</h1>
            <?php

            $domain = array( 1 => "Oui", 2=>"Non", 3=>"Peut-être", 4=>"Je ne sais pas lire" );

            // Créé une liste de champs de base
            $fields = array(
                new FormFieldBoolean( 'bool', false, false, 'FormFieldBoolean', null, 'Booléen Checkbox (ne peut pas être obligatoire)' ),
                new FormFieldYesNo( 'yesno', true, false, 'FormFieldYesNo', null, 'Booléen oui/non (peut être obligatoire)' ),

            // différents types de sélecteurs
            // les clés peuvent être des nombres (incluant 0) ou des strings
                new FormFieldDomain( 'selector', false, false, 'FormFieldDomain', null, "Sélecteur standard", $domain ),
                new FormFieldRadio( 'radio', false, false, 'FormFieldRadio', null, "Sélecteur à boutons radio", $domain ),
                new FormFieldMultiple( 'checkboxes', false, false, 'FormFieldMultiple', null, "Sélecteur multiple à checkbox", $domain ),
            );

            // Créé le form
            $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a été validé
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour expérimentation
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

            // Créé une liste de champs de base
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
                    'Fichier à envoyer (image jpeg ou png, 800x600 max, 512k max)', null, '' ),
                new FormFieldString( 'filename', true, new FormValidatorPerlRegexp( 1,50, '/^[a-z0-9_]+\.[a-z0-9]+$/', "Le nom et l'extension doivent se composer uniquement de minuscules et de chiffres" ), 'Nom à donner à ce fichier sur le serveur', null, 'Le fichier ne sera pas sera sauvé.' ),
            );

            // Créé le form
            $form = new FormBase(   'example', url_from_get_vars( false ), false, $fields );

            if( $form->Process() )
            {
                // Si on arrive ici, c'est que tout a été validé
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour expérimentation
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
                // Si on arrive ici, c'est que tout a été validé
                ?><h3>Informations extraites du formulaire :</h3><?php

                // Affiche les informations brutes
                xdump( $form->ExtractValues() );

                // Affiche les informations, joli
                $form->DisplayHTML();

                // remet le formulaire pour expérimentation
                $form->DisplayForm();
            }
            break;
        
        case 8:
            highlight_file( __FILE__ );
    }


