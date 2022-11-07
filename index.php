<?php
include_once "conexao.php";
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <title>Celke</title>
</head>

<body>
    <?php
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT); 
    ?>

    <form method="POST" action="">
        <label>Pesquisar: </label><br><br>
        <?php
            //Receber os dados do formulário para manter selecionado os checkbox
            $valor_pesq_list ="";
            if(!empty($dados['nivel_acesso'])){
                foreach($dados['nivel_acesso'] as $nivel_acesso_id){
                    $valor_pesq_list .= "$nivel_acesso_id, ";
                }
            }

            //var_dump($valor_pesq_list);

            //Pesquisa os níveis de acesso no BD
            $query_niveis_acessos= "SELECT id, nome FROM niveis_acessos ORDER BY nome ASC";
            $result_niveis_acessos = $conn->prepare($query_niveis_acessos);
            $result_niveis_acessos->execute();

            while($row_nivel_acesso = $result_niveis_acessos->fetch(PDO::FETCH_ASSOC)){
                //var_dump($row_nivel_acesso);
                extract($row_nivel_acesso);  
                $result_valor = mb_strpos($valor_pesq_list, $id);
                if($result_valor === false){
                    $checked = "";
                }else{
                    $checked = "checked";
                }
                echo "<input type='checkbox' name='nivel_acesso[]' value='$id' $checked>$nome <br>";
            }
        ?>

        <br><input type="submit" value="Pesquisar" name="PesqUsuarios"><br><br>
    </form>

    <?php
        if(!empty($dados['PesqUsuarios'])){
            //var_dump($dados);

            $valor_pesq = "";
            $controle = 1;
            if(!empty($dados['nivel_acesso'])){
                foreach($dados['nivel_acesso'] as $niveis_acesso_id){
                    if($controle == 1 ){
                        $valor_pesq .= $niveis_acesso_id;
                    }else{
                        $valor_pesq .= ", $niveis_acesso_id";
                    }
                    $controle++;
                }
            }           

            //var_dump($valor_pesq);

            $query_usuarios = "SELECT usr.id, usr.nome nome_usr, usr.email, usr.niveis_acesso_id,
                        niv.nome nome_niv
                        FROM usuarios AS usr
                        INNER JOIN niveis_acessos AS niv ON niv.id=usr.niveis_acesso_id
                        WHERE usr.niveis_acesso_id IN ($valor_pesq)";
            $result_usuarios = $conn->prepare($query_usuarios);
            $result_usuarios->execute();

            while($row_usuario = $result_usuarios->fetch(PDO::FETCH_ASSOC)){
                extract($row_usuario);
                echo "Id: $id <br>";
                echo "Nome: $nome_usr <br>";
                echo "E-mail: $email <br>";
                echo "Nível de Acesso: $nome_niv <br>";
                echo "<hr>";
            }
        }
    ?>

</body>

</html>