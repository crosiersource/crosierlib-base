<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\TestsUtils;

use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;

/**
 * Classe utilitária para geração de dados úteis para testes.
 *
 * @author Carlos Eduardo Pauluk
 */
class GeneratorsUtils
{

    public static function genNome(?bool $comSobrenome = true)
    {
        $nomes = [
            'Helena', 'Alice', 'Laura', 'Manuela', 'Valentina', 'Sophia', 'Isabella', 'Heloísa', 'Luiza', 'Júlia', 'Lorena', 'Lívia', 'Maria Luiza', 'Cecília', 'Eloá', 'Giovanna', 'Maria Clara', 'Maria Eduarda', 'Mariana', 'Lara', 'Beatriz', 'Antonella', 'Maria Júlia', 'Emanuelly', 'Isadora', 'Ana Clara', 'Melissa', 'Ana Luiza', 'Ana Júlia', 'Esther', 'Lavínia', 'Maitê', 'Maria Cecília', 'Maria Alice', 'Sarah', 'Elisa', 'Liz', 'Yasmin', 'Isabelly', 'Alícia', 'Clara', 'Isis', 'Rebeca', 'Rafaela', 'Marina', 'Ana Laura', 'Maria Helena', 'Agatha', 'Gabriela', 'Catarina', 'Miguel', 'Arthur', 'Heitor', 'Bernardo', 'Davi', 'Théo', 'Lorenzo', 'Gabriel', 'Pedro', 'Benjamin', 'Matheus', 'Lucas', 'Nicolas', 'Joaquim', 'Samuel', 'Henrique', 'Rafael', 'Guilherme', 'Enzo', 'Murilo', 'Benício', 'Gustavo', 'Isaac', 'João Miguel', 'Lucca', 'Enzo Gabriel', 'Pedro Henrique', 'Felipe', 'João Pedro', 'Pietro', 'Anthony', 'Daniel', 'Bryan', 'Davi Lucca', 'Leonardo', 'Vicente', 'Eduardo', 'Gael', 'Antônio', 'Vitor', 'Noah', 'Caio', 'João', 'Emanuel', 'Cauã', 'João Lucas', 'Calebe', 'Enrico', 'Vinícius', 'Bento'
        ];

        $sobrenomes = [
            'Abreu', 'Adães', 'Adorno', 'Aguiar', 'Albuquerque', 'Alcântara', 'Aleluia', 'Alencar', 'Almeida', 'Altamirano', 'Alvarenga', 'Álvares', 'Alves', 'Alvim', 'Amaral', 'Amigo', 'Amor', 'Amorim', 'Anchieta', 'Andrada', 'Andrade', 'Anes', 'Anjos', 'Antunes', 'Anunciação', 'Aragão', 'Araújo', 'Arruda', 'Ascensão', 'Assis', 'Azeredo', 'Azevedo', 'Bandeira', 'Barbosa', 'Barros', 'Barroso', 'Bastos', 'Batista', 'Bermudes', 'Bernades', 'Bernardes', 'Bicalho', 'Bispo', 'Bocaiuva', 'Bolsonaro', 'Borba', 'Borges', 'Borsoi', 'Botelho', 'Braga', 'Bragança', 'Brandão', 'Brasil', 'Brasiliense', 'Bueno', 'Cabral', 'Café', 'Camacho', 'Camargo', 'Caminha', 'Camões', 'Cardoso', 'Carmo', 'Carnaval', 'Carneiro', 'Carvalhal', 'Carvalho', 'Carvalhosa', 'Castilho', 'Castro', 'Cerejeira', 'Chaves', 'Coelho', 'Coentrão', 'Coimbra', 'Constante', 'Cordeiro', 'Costa', 'Cotrim', 'Couto', 'Coutinho', 'Cruz', 'Cunha', 'Curado', 'Dambros', 'Dias', 'Diegues', 'Dorneles', 'Duarte', 'Eça', 'Encarnação', 'Esteves', 'Evangelista', 'Exaltação', 'Fagundes', 'Faleiros', 'Falópio', 'Falqueto', 'Faria', 'Farias', 'Faro', 'Ferrão', 'Ferraz', 'Ferreira', 'Ferrolho', 'Fernandes', 'Figo', 'Figueira', 'Figueiredo', 'Figueiroa', 'Fioravante', 'Fonseca', 'Fontes', 'Fortaleza', 'França', 'Freire', 'Freitas', 'Frota', 'Furquim', 'Furtado', 'Galvão', 'Gama', 'Garrastazu', 'Gomes', 'Gonçales', 'Gonçalves', 'Gonzaga', 'Gouveia', 'Guimarães', 'Gusmão', 'Henriques', 'Hernandes', 'Holanda', 'Homem', 'Hora', 'Hungria', 'Jardim', 'Junqueira', 'Lacerda', 'Lange', 'Leite', 'Leme', 'Lins', 'Locatelli', 'Lopes', 'Luz', 'Macedo', 'Machado', 'Madureira', 'Maduro', 'Magalhães', 'Mairinque', 'Malafaia', 'Malta', 'Mariz', 'Marques', 'Martins', 'Massa', 'Matos', 'Médici', 'Meireles', 'Mello', 'Melo', 'Mendes', 'Mendonça', 'Menino', 'Mesquita', 'Miranda', 'Moraes', 'Morais', 'Morato', 'Moreira', 'Moro', 'Monteiro', 'Muniz', 'Nantes', 'Nascimento', 'Navarro', 'Naves', 'Negreiros', 'Negrete', 'Neves', 'Nóbrega', 'Nogueira', 'Noronha', 'Nunes', 'Oliva', 'Oliveira', 'Outeiro', 'Pacheco', 'Padrão', 'Paes', 'Pais', 'Paiva', 'Paixão', 'Papanicolau', 'Parga', 'Pascal', 'Pascoal', 'Pasquim', 'Patriota', 'Peçanha', 'Pedrosa', 'Pedroso', 'Peixoto', 'Pensamento', 'Penteado', 'Pereira', 'Peres', 'Pessoa', 'Pestana', 'Pimenta', 'Pimentel', 'Pinheiro', 'Pires', 'Poeta', 'Policarpo', 'Porto', 'Portugal', 'Prado', 'Prudente', 'Quaresma', 'Queirós', 'Queiroz', 'Ramalhete', 'Ramalho', 'Ramires', 'Ramos', 'Rangel', 'Reis', 'Resende', 'Ribeiro', 'Rios', 'Rodrigues', 'Roma', 'Romão', 'Sá', 'Sacramento', 'Sampaio', 'Sampaulo', 'Sampedro', 'Sanches', 'Santacruz', 'Santana', 'Santander', 'Santarrosa', 'Santiago', 'Santos', 'Saragoça', 'Saraiva', 'Saramago', 'Seixas', 'Serra', 'Serrano', 'Silva', 'Silveira', 'Simões', 'Siqueira', 'Soares', 'Soeiro', 'Sousa', 'Souza', 'Tavares', 'Teixeira', 'Teles', 'Torquato', 'Trindade', 'Uchoa', 'Uribe', 'Ustra', 'Valadares', 'Valença', 'Valente', 'Varela', 'Vasconcelos', 'Vasques', 'Vaz', 'Veiga', 'Velasques', 'Veloso', 'Viana', 'Vieira', 'Vilela', 'Vilhena', 'Xavier', 'Zampol'
        ];

        $nome = array_rand($nomes);
        if ($comSobrenome) {
            $nome .= ' ' . array_rand($sobrenomes);
        }

        return $nome;
    }

    
    /**
     * @param int|null $idadeMinima
     * @param int|null $idadeMaxima
     * @return \DateTime|null
     */
    public static function genDtNascimento(?int $idadeMinima = 0, ?int $idadeMaxima = 120)
    {
        $esteAno = (new \DateTime())->format('Y');
        $dia = [
            1 => rand(1, 31),
            2 => rand(1, 28),
            3 => rand(1, 31),
            4 => rand(1, 30),
            5 => rand(1, 31),
            6 => rand(1, 30),
            7 => rand(1, 31),
            8 => rand(1, 31),
            9 => rand(1, 30),
            10 => rand(1, 31),
            11 => rand(1, 30),
            12 => rand(1, 31),
        ];
        $mes = rand(1, 12);
        return DateTimeUtils::parseDateStr($esteAno - rand($idadeMinima, $idadeMaxima), $mes, $dia[$mes]);
    }


    /**
     * @param int|null $ddd
     * @param bool|null $celular
     */
    public static function genTelefone(?int $ddd = null, ?bool $celular = true, ?bool $formatado = true)
    {
        $ddds = [61, 62, 64, 65, 66, 67, 82, 71, 73, 74, 75, 77, 85, 88, 98, 99, 83, 81, 87, 86, 89, 84, 79, 68, 96, 92, 97, 91, 93, 94, 69, 95, 63, 27, 28, 31, 32, 33, 34, 35, 37, 38, 21, 22, 24, 11, 12, 13, 14, 15, 16, 17, 18, 19, 41, 42, 43, 44, 45, 46, 51, 53, 54, 55, 47, 48, 49];
        $telefone = ($ddd ? $ddd : array_rand($ddds)) . ($celular ? rand(80, 99) : 3) . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        return $formatado ? StringUtils::formataTelefone($telefone) : $telefone;
    }

}