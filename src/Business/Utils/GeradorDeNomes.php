<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Utils;

class GeradorDeNomes
{

    public static function gerarNomeMasculino(): string
    {
        return self::gerarNome(true);
    }

    public static function gerarNomeFeminino(): string
    {
        return self::gerarNome(false);
    }

    public static function gerarNome(bool $masculino = true): string
    {
        if ($masculino) {
            $nome = self::getCompostoOuNao(self::getPrimeirosNomesMasculinos()) . ' ' . self::getCompostoOuNao(self::getSobrenomes());
            $r = rand(0, 100);
            if ($r < 5) {
                $nome .= ' JUNIOR';
            } elseif ($r > 95) {
                $nome .= ' NETO';
            }
        } else {
            $nome = self::getCompostoOuNao(self::getPrimeirosNomesFemininos()) . ' ' . self::getCompostoOuNao(self::getSobrenomes());
        }
        return mb_strtoupper($nome);
    }


    public static function gerarNomeEmpresarial(): array
    {
        $ramoEmpresa = self::getRamosDeEmpresas()[rand(0, count(self::getRamosDeEmpresas()) - 1)];
        $sobrenome = self::getCompostoOuNao(self::getSobrenomes());
        $tipoEmpresa = self::getTiposDeEmpresas()[rand(0, count(self::getTiposDeEmpresas()) - 1)];
        return [
            'razaoSocial' => mb_strtoupper($ramoEmpresa . ' ' . $sobrenome . ' ' . $tipoEmpresa),
            'nomeFantasia' => mb_strtoupper($ramoEmpresa . ' ' . $sobrenome),
        ];
    }


    private static function getCompostoOuNao(array $lista): string
    {
        $nome = $lista[rand(0, count($lista) - 1)];
        if (strpos($nome, ' ') === false && rand(0, 10) > 7) {
            // remove $nome from $nomes
            $listaSemElemento = array_values(array_diff($lista, [$nome]));
            $segundoNome = $listaSemElemento[rand(0, count($listaSemElemento) - 1)];
            $nome .= ' ' . $segundoNome;
        }
        return $nome;
    }

    private static function getPrimeirosNomesFemininos(): array
    {
        return ['Agatha', 'Alana', 'Alice', 'Alícia', 'Amanda', 'Ana', 'Ana Beatriz', 'Ana Carolina', 'Ana Clara', 'Ana Julia', 'Ana Laura', 'Ana Lívia', 'Ana Luiza', 'Ana Sophia', 'Antonella', 'Bárbara', 'Beatriz', 'Betina', 'Bianca', 'Brenda', 'Bruna', 'Camila', 'Carolina', 'Caroline', 'Catarina', 'Cecília', 'Clara', 'Débora', 'Eduarda', 'Elisa', 'Eloá', 'Emanuelly', 'Emily', 'Ester', 'Evelyn', 'Fernanda', 'Gabriela', 'Gabrielly', 'Giovanna', 'Helena', 'Heloisa', 'Isabel', 'Isabella', 'Isabelle', 'Isadora', 'Jennifer', 'Joana', 'Julia', 'Juliana', 'Kamilly', 'Laís', 'Lara', 'Larissa', 'Laura', 'Lavínia', 'Letícia', 'Lívia', 'Lorena', 'Luana', 'Luiz', 'Luiza', 'Luna', 'Maitê', 'Manuela', 'Marcela', 'Maria', 'Maria Alice', 'Maria Cecília', 'Maria Clara', 'Maria Eduarda', 'Maria Fernanda', 'Mariah', 'Maria Júlia', 'Maria Luiza', 'Mariana', 'Mariane', 'Maria Sophia', 'Maria Vitória', 'Marina', 'Melissa', 'Milena', 'Mirella', 'Natália', 'Nicole', 'Nina', 'Olivia', 'Pietra', 'Rafaela', 'Raquel', 'Rayssa', 'Rebeca', 'Sabrina', 'Sarah', 'Sophia', 'Sophie', 'Stefany', 'Stella', 'Valentina', 'Vitória', 'Yasmin',];
    }

    private static function getPrimeirosNomesMasculinos(): array
    {
        return ['Alexandre', 'André', 'Anthony', 'Antonio', 'Arthur', 'Augusto', 'Benício', 'Benjamin', 'Bernardo', 'Breno', 'Bruno', 'Bryan', 'Caio', 'Calebe', 'Carlos Eduardo', 'Cauã', 'Cauê', 'Daniel', 'Danilo', 'Davi', 'Diego', 'Diogo', 'Eduardo', 'Emanuel', 'Enrico', 'Enzo', 'Enzo Gabriel', 'Erick', 'Felipe', 'Fernando', 'Francisco', 'Gabriel', 'Giovanni', 'Guilherme', 'Gustavo', 'Heitor', 'Henrique', 'Henry', 'Hugo', 'Iago', 'Ian', 'Igor', 'Isaac', 'João', 'João Gabriel', 'João Guilherme', 'João Lucas', 'João Miguel', 'João Paulo', 'João Pedro', 'João Vitor', 'Joaquim', 'Juan', 'Julio César', 'Kaique', 'Kevin', 'Leonardo', 'Levi', 'Lorenzo', 'Luan', 'Lucas', 'Lucas Gabriel', 'Lucca', 'Luiz Felipe', 'Luiz Fernando', 'Luiz Guilherme', 'Luiz Gustavo', 'Luiz Henrique', 'Luiz Miguel', 'Luiz Otávio', 'Marcelo', 'Marcos Vinicius', 'Matheus', 'Matheus Henrique', 'Miguel', 'Murilo', 'Nathan', 'Nicolas', 'Otávio', 'Pedro', 'Pedro Henrique', 'Pedro Lucas', 'Pietro', 'Rafael', 'Raul', 'Renan', 'Renato', 'Ricardo', 'Rodrigo', 'Ryan', 'Samuel', 'Thales', 'Theo', 'Thiago', 'Tomás', 'Vinicius', 'Vitor', 'Vitor Gabriel', 'Vitor Hugo', 'Yuri'];
    }

    private static function getSobrenomes(): array
    {
        return ['ABREU', 'ABROMOVIZT', 'ABUEEAR', 'ADAMECK', 'ADAMS', 'ADRIANO', 'AGUIAR', 'AHID', 'ALBANAS', 'ALBERS', 'ALBERT', 'ALBERTON', 'ALBINO', 'ALENCAR', 'ALFARO', 'ALFLEN', 'ALLEIN', 'ALMEIDA', 'ALTENBURG', 'ALTHOFF', 'ALVES', 'AMARAL', 'AMARANTE', 'AMBRÓSIO', 'AMORIM', 'ANDRADE', 'ANDRETTA', 'ANGEOLETE', 'ANICETO', 'ANICETTO', 'ANNA', 'ANÔNIMA', 'ANÔNIMO', 'ANTUNES', 'APARÍCIO', 'ARAUJO', 'ARENS', 'ARNDT', 'ARNOLD', 'ARNS', 'ASEVEDO', 'ASSING', 'ASSIS', 'ASVILA', 'AUXILIADORA', 'AVILA', 'AZAMBUJA', 'AZEVEDO', 'BACK', 'BACKES', 'BADO', 'BAKES', 'BALDESSIN', 'BALTAZAR', 'BAPTISTA', 'BARBOSA', 'BARDEN', 'BARDINI', 'BARNI', 'BARRA', 'BARRETO', 'BARROS', 'BARTH', 'BARTHES', 'BASON', 'BASTOS', 'BATISTA', 'BAUER', 'BAUMER', 'BAUNGARTEN', 'BAY', 'BAYER', 'BECHER', 'BECHTOLD', 'BECKER', 'BECSI', 'BEHLING', 'BEHRENS', 'BEISTORF', 'BELARMINA', 'BELARMINO', 'BELEGANTE', 'BENCKE OU BENK', 'BENKE', 'BENNERT', 'BENTO DA SILVA', 'BEPE', 'BEPLER', 'BEPPLER', 'BERETTA', 'BERGER', 'BERGLER', 'BERGMANN', 'BERK', 'BERKENBROCK', 'BERLANDA', 'BERN', 'BERNARDES', 'BERNDT', 'BERNS', 'BERTHOLDI', 'BERTO', 'BERTOLDI', 'BERTOLI', 'BERTONCINI', 'BESCHINOCK', 'BESEN', 'BESSA', 'BEZERRA', 'BIANCHINI', 'BIANKM', 'BIEZUS', 'BILK', 'BINS', 'BIONDORO', 'BITENCOURT', 'BITTAR', 'BITTELBRUN', 'BITTENCOURT', 'BLÄSER', 'BLOEMER', 'BLOSFELD', 'BLUMEN', 'BOAVENTURA', 'BOEING', 'BOGO', 'BOHNE', 'BOHNEN', 'BÖHS', 'BOHUN', 'BÖING', 'BOMBAZAR', 'BOMBILIO', 'BÖMMEL', 'BONETTO', 'BONFIM', 'BONILAURI', 'BONIN', 'BOOM', 'BORBA', 'BORGES', 'BORNHOFEN', 'BORNHOFER', 'BORTOLON', 'BORTOLUZZI', 'BOSS', 'BOT', 'BOTT', 'BOYON', 'BOZZANO', 'BRAATZ', 'BRAGA', 'BRAHMS', 'BRANCO', 'BRAND', 'BRANDT', 'BRASIL', 'BRATFICH', 'BREZINSKI', 'BRICK', 'BRILLINGER', 'BRINCAS', 'BRITO', 'BROERING', 'BRÖRING', 'BRUDER', 'BRÜGGEMANN', 'BRÜGGMANN', 'BRUNELLI', 'BUCHMANN', 'BÜCKLER', 'BÜCLER', 'BUDAG', 'BUDINGER', 'BÜGLER', 'BUGMANN', 'BUHRBRINKER', 'BUNN', 'BURATO', 'BURK', 'BUSO', 'BUSS', 'BUTENAROWSKY', 'BUTKE', 'BÜTTINGER', 'BUTZKE', 'BUZZI', 'CABRAL', 'CACHOEIRA', 'CADORI', 'CAETANO', 'CALBUCH', 'CALEGARI', 'CAMARGO', 'CAMINO', 'CAMPOS', 'CAMPRESTINI', 'CANDIDA', 'CÂNDIDO', 'CANELLA', 'CAPISTRANO', 'CARAPUNARLO', 'CARDOSO', 'CARIONI', 'CARLOS', 'CARMINATTI', 'CARMINDA', 'CARMO', 'CAROLINA', 'CARRARO', 'CARVALHO', 'CASEDES', 'CASTANHEIRO', 'CASTILHO', 'CASTRO', 'CATHARINA', 'CAVA', 'CECHINEL', 'CELI', 'CELLARIUS', 'CENSI', 'CESTILE', 'CHAGAS', 'CHANG', 'CHAPLIN', 'CHAVES', 'CHIODINI', 'CHIQUETTE', 'CIPRIANI', 'CIUS', 'CIVINSKI', 'CLASEN', 'CLAUDINO', 'CLEMENTE', 'CLETO', 'COELHO', 'COMPER', 'CONCEIÇÃO', 'CONCER', 'CONRADI', 'CONSTANT', 'CORADELLI', 'CORDEIRO', 'CORDOVA', 'CORLETTO', 'CORRADINI', 'CORRÊA', 'CORREIA', 'COSTA', 'COULING', 'COUTO', 'COVALSKI', 'CRISPIM', 'CRISTIANO', 'CRISTOFOLINI', 'CROSS', 'CRUZ', 'CUBAN', 'CUNHA', 'CUSTÓDIA', 'CUSTÓDIO', 'CYRINO', 'DALÇOQUIO', 'DALMARCO', 'DALMOLIN', 'DALMORA', 'DALSENTER', 'DAMA', 'DAMANN', 'DAMÁSIO', 'DAUFENBACH', 'DAVILA', 'DEBATIN', 'DECHERING', 'DEGREGORI', 'DELFINO', 'DELLALIBERA', 'DELLÊ', 'DELLÊ NETTO', 'DELUCA', 'DEMARCHI', 'DENK', 'DERÉNUSSON', 'DESCHAMPS', 'DESCONHECIDA', 'DESCONHECIDO', 'DESCONHEIDA', 'DETZEL', 'DEUCHER', 'DEWES', 'DIAS', 'DIEDERICH', 'DIEDMANN', 'DIEL', 'DIGNOLI', 'DILL', 'DIRKSEN', 'DOBRANZ', 'DOERNER', 'DOMINONI', 'DORNA', 'DÖRNER', 'DREER', 'DROSDA', 'DUARTE', 'DUBIELA', 'DUBIELLA', 'DÜMMES', 'DUTRA', 'DUWE', 'EDUARDO', 'EGER', 'EHRHARDT', 'EIFELER', 'EIFLER', 'EING', 'EISELER', 'ELI', 'ELIAS', 'ELLARD', 'ELLY', 'ELY', 'EMILIA', 'ENDER', 'ENGELMANN', 'ENTRE', 'EPIFÂNIO', 'ERHADT', 'ERHARDT', 'ERN', 'ERTHAL', 'ESPER', 'ESPINDOLA', 'ESSER', 'ESTEVES', 'ESTRELA', 'EUERLAND', 'EUGÊNIO', 'EULINA', 'EYNG', 'FABIANE', 'FACCHINI', 'FACHINI', 'FAGUNDES', 'FAHT', 'FAIGLE', 'FALLER', 'FARIA', 'FARIAS', 'FAUST', 'FAUSTINO', 'FEDHAUS', 'FEDRIGO', 'FEIBER', 'FEIDER', 'FEIJÓ', 'FEILER', 'FELAÇO', 'FELBER', 'FELDHAUS', 'FELER', 'FELETTI DE SOUZA', 'FELICIANO', 'FELIPPUS', 'FELISBERTA', 'FELISBERTO', 'FELLIPPE', 'FELZER', 'FERMÖHLEN', 'FERNANDES', 'FERRARI', 'FERREIRA', 'FERRETTI', 'FERRON', 'FIDUNIV', 'FIGUEIREDO', 'FILA', 'FILAGRANA', 'FILIPPUS', 'FILLA', 'FINKBEINER', 'FISCHER', 'FIUZA', 'FLEITH', 'FLOR', 'FLORENTINA', 'FONSECA', 'FONTANIVE', 'FORTKAMP', 'FORTUNATO', 'FOSS', 'FOSTER', 'FRAGA', 'FRAGAS', 'FRANÇA', 'FRANCENER', 'FRANCIOSI', 'FRANCIOZI', 'FRANZ', 'FRANZENER', 'FRANZER', 'FRANZOI', 'FREIBERGER', 'FREITAS', 'FREYN', 'FRIEDRICHS', 'FRITZEN', 'FRITZER', 'FROTKAMP', 'FRUCTUOSO', 'FRUTUOSO', 'FUCK', 'FURLANETTO', 'FURTUOSO', 'FUZÃO', 'GABARRÃO', 'GADOTTI', 'GAERTNER', 'GAIDA', 'GALDINO', 'GANSEN', 'GARCIA', 'GARDOLIN', 'GAROZI', 'GASPARELLO', 'GASPAROTO', 'GAULKE', 'GAULOSKI', 'GAZOLA', 'GERBER', 'GERENT', 'GERLACH', 'GERMANA', 'GESING', 'GESSER', 'GEVAERD', 'GIACOMELLI', 'GILSA', 'GILZ', 'GIPP', 'GNEDEZ', 'GOCKS', 'GÖDERT', 'GOEBEL', 'GOEDERT', 'GÖLDERT', 'GOMES', 'GONÇALVES', 'GORGES', 'GÖRRES', 'GOULART', 'GRACIA', 'GRAFER', 'GRAH', 'GRAHL', 'GRANEMANN', 'GREIPEL', 'GRENTESKI', 'GRIPPA', 'GROSCH', 'GROSS', 'GUCKERT', 'GUEBBEL', 'GUEBERTH', 'GUEDES', 'GUERRA', 'GUESSER', 'GUETTMANN', 'GUIMARÃES', 'GUTHEMBERG', 'GUTS', 'HAAN', 'HADLICH', 'HALL', 'HALLA', 'HAMANN', 'HAMMES', 'HAMS', 'HÄNDECHEN', 'HANG', 'HANK', 'HARGER', 'HARRES', 'HARTKOPFF', 'HASCKEL', 'HASKEL', 'HASS', 'HASSE', 'HASSMANN', 'HAUSMANN', 'HAVERROTH', 'HECK', 'HEERDT', 'HEIDENREICH', 'HEIDERSCHEID', 'HEIDORN', 'HEIL', 'HEINZ', 'HEINZEN', 'HELFER', 'HELLMANN', 'HEMKEMAIER', 'HENRIQUES', 'HERCHENBACH', 'HERING', 'HERMEN', 'HERMESMEYER', 'HESSMANN', 'HILLEBRANDT', 'HILLESHEIM', 'HILLESHEIN', 'HILLHESHEIM', 'HINCKEL', 'HINGHAUS', 'HINKEL', 'HINTEMANN', 'HOCHAPFEL', 'HOEGEN', 'HOELLER', 'HOEPERS', 'HOFFMAN', 'HOFFMANN', 'HOLLER', 'HOLTHAUSEN', 'HÖPER', 'HORNSCHU', 'HORST', 'HORSTMANN', 'HORTMANN', 'HOSTEMANN', 'HOSTEMENN', 'HUBER', 'HÜBNER', 'HULLER', 'HULRICH', 'HUNGARO', 'HÜNTEMANN', 'IADOCICCO', 'IANZEN', 'IBERS', 'INÁCIO', 'IOCHEN', 'IOSHIMI', 'IZABEL', 'JAHM', 'JAIME', 'JAIR', 'JAME', 'JANMING', 'JANNING', 'JANSEN', 'JARASESKI', 'JARRATCHESKY', 'JASPER', 'JENDIGK', 'JENSEN', 'JEREMIAS', 'JESUINA', 'JESUS', 'JOCHE', 'JOCHEM', 'JOCHEN', 'JOENK', 'JONCH', 'JÖNCK', 'JÖNCKE', 'JÖNCKE', 'JONKERS', 'JOSÉ', 'JOSINO', 'JUKA', 'JUNCKES', 'JUNGLAS', 'JUNKER', 'JUNKERS', 'JUNKES', 'JUNKLAUS', 'JUPPA', 'JUST', 'JUSTEN', 'JUSTI', 'JUSTINO', 'JUSTO', 'JUTTEL', 'JUTTELL', 'KAAL', 'KAHL', 'KAHLFELS', 'KALBUSCH', 'KALLFELS', 'KALLFELZ', 'KAMMER', 'KAMMERS', 'KAMPH', 'KANDT', 'KARSTEN', 'KASPERS', 'KATAOKA', 'KAULING', 'KAVACO', 'KEHRIG', 'KEHRING', 'KEMPNER', 'KENKEL', 'KEPPLER', 'KERSBAUM', 'KERSCHBAUM', 'KESTRING', 'KICHNER', 'KIEFER', 'KIPFER', 'KIRCHNER', 'KIRSCH', 'KLANN', 'KLASEN', 'KLAUMANN', 'KLEIN', 'KLEINER', 'KLETTENBERG', 'KLÖPPEL', 'KNABBEN', 'KNABEN', 'KNAUL', 'KNIHS', 'KNISS', 'KNIZ', 'KNOLL', 'KOCH', 'KOCIAN', 'KOEHLER', 'KOEPSEL', 'KOERICH', 'KÖLLER', 'KON', 'KONS', 'KOPICH', 'KÖPP', 'KORNER', 'KOSMANN', 'KRÄMER', 'KRAPP', 'KRATZ', 'KRAUS', 'KRAUSE', 'KRAUZE', 'KREFF', 'KREMER', 'KREMMER', 'KRETZER', 'KREUSCH', 'KRIEGER', 'KRISTEL', 'KRÜGER', 'KUESSNER', 'KÜHLKAMP', 'KUHN', 'KUHNEN', 'KUIJK', 'KUIPERS', 'KUNTZ', 'KUNTZE', 'KURNER', 'KURTZ', 'KUSMA', 'KÜSTER', 'LAMEU', 'LAMIM', 'LANGE', 'LANSNASTER', 'LARANJA', 'LAUBEN', 'LAUDELINA', 'LAURENTINO', 'LAURINDO', 'LAUX', 'LAZZARI', 'LEAL', 'LEBARBENCHON', 'LEBERARDT', 'LEBERBENCHON', 'LEHMKUHL', 'LEINECKER', 'LEITE', 'LEMIEUX', 'LEMOS', 'LENTZEN', 'LENZI', 'LEONOR', 'LEVECK', 'LEWEN', 'LILGEN', 'LIMA', 'LIMAS', 'LINA', 'LINHARES', 'LISBÔA', 'LIVI', 'LIZ', 'LOBO', 'LOCH', 'LOEFF', 'LOFFI', 'LOFY', 'LOHN', 'LOMBARDI', 'LONGEN', 'LONGHI', 'LOPES', 'LORENA', 'LOURDES', 'LOUZADA', 'LUCAS', 'LUCHTEMBERG', 'LUCHTTENBERG', 'LUCIANI', 'LUCIANO', 'LUCKMANN', 'LUDVIG', 'LUDWIG', 'LUECKMANN', 'LUIZ', 'LUZ', 'MABA', 'MAÇANEIRO', 'MACEDO', 'MACHADO', 'MACIEL', 'MADALENA', 'MADRUGA', 'MAENNCHEN', 'MAFRA', 'MAGGIO', 'MAGNA', 'MAIA', 'MAIOCH', 'MAIOCHI', 'MANARCHI', 'MANDEL', 'MANHÃES', 'MANHÕES', 'MANNRICH', 'MARÃES', 'MARANGONI', 'MARCELINO', 'MARCHI', 'MARCHINIAK', 'MARCILIO', 'MARCOLLA', 'MARCOS', 'MAREGA', 'MARIAM', 'MARIAN', 'MARIANO', 'MARIN', 'MARQUES', 'MARQUEZ', 'MARTENDAHL', 'MARTENDAL', 'MARTENDHAL', 'MARTENTHAL', 'MARTHENDAHL', 'MARTHENDAL', 'MARTINELLI', 'MARTINI', 'MARTINS', 'MASSELAI', 'MATHENTAL', 'MATOS', 'MATTES', 'MATTIA', 'MATTOS', 'MAY', 'MAYER', 'MAZZOCHI', 'MEDEIROS', 'MEES', 'MEIER', 'MEINSCHEIN', 'MEIRA', 'MELLO', 'MELO', 'MELZ', 'MENCHEIM', 'MENDES', 'MENDONÇA', 'MERGES', 'MERHY', 'MERÍZIO', 'MERTEN', 'MERTES', 'MESS', 'MEURER', 'MEYER', 'MICHELS', 'MICHOLET', 'MIGLIOLLI', 'MIGUEL', 'MILACCI', 'MINELLI', 'MINICH', 'MIÑO', 'MINTIN', 'MIOTTO', 'MIRANDA', 'MISCÔ', 'MOHR', 'MOIER', 'MOISÉS', 'MOLINARI', 'MOMM', 'MONTAGNA', 'MONTEIRO', 'MONTIBELLER', 'MORAES', 'MORAIS', 'MOREIRA', 'MORESCHI', 'MORESCO', 'MORETTI', 'MORETTO', 'MÖSS', 'MOTA', 'MOTTA', 'MÜLLER', 'MÜLNITZ', 'MUNIZ', 'NACK', 'NADALIN', 'NAM', 'NASCHENWENG', 'NASCHNWENG', 'NASCIMENTO', 'NAU', 'NAUMEST', 'NAZARIO', 'NECKEL', 'NEHRING', 'NENEMANN', 'NERCOLINI', 'NEUHAUS', 'NEVES', 'NICHAEL', 'NICHETTI', 'NICOLAZZI', 'NIENCHOTTER', 'NIENCKÖTTER', 'NIENKÖTTER', 'NINKÖTHER', 'NIQUELATO', 'NOGARA', 'NORILER', 'NOTTINGHAM', 'NOVAC', 'NUNES', 'NÜRNBERG', 'ODERDING', 'OLICA', 'OLIVEIRA', 'ONING', 'ONNING', 'ONO', 'ONOFRE', 'OOMMENN', 'ORSI', 'ORTHMANN', 'OTTIQUIR', 'OTTO', 'PACHECO', 'PADILHA', 'PAIM', 'PAIVA', 'PALHANO', 'PAMPLONA', 'PANTOJA', 'PAREDES', 'PARENTE', 'PARMA', 'PAROLIN', 'PARZEWSKI', 'PASSIG', 'PASSING', 'PASSOS', 'PATRUNI', 'PAULETO', 'PAULI', 'PAULINO', 'PEDRINI', 'PEIXE', 'PEIXER', 'PELLENZ', 'PERADT', 'PERARDT', 'PEREIRA', 'PERES', 'PERHARDT', 'PESSOA', 'PETRI', 'PETRY', 'PETTLER', 'PEZENTI', 'PFLEGER', 'PHILIPPI', 'PICKLER', 'PIERR', 'PIERRO', 'PINHEIRO', 'PINHO', 'PINTO', 'PIRÃO', 'PIRES', 'PIRHARDT', 'PITS', 'PITZ', 'PIVATTO', 'PLETI', 'POLINI', 'POLLI', 'POPP', 'POPPER', 'PORTER', 'POSTAIS', 'PRADO', 'PREISS', 'PRESTES', 'PRETTI', 'PRETZ', 'PRIM', 'PRIMM', 'PROBST', 'PROCHNOW', 'PROCURADA', 'PROCURADO', 'PROCURA-SE', 'PRUDÊNCIO', 'PRUST', 'PÜTZ', 'PUTZER', 'QUADROS', 'RABE', 'RABELO', 'RADEL', 'RAHN', 'RAITZ', 'RAMLOW', 'RAMOS', 'RAMPINELLI', 'RANCONI', 'RASSWEILER', 'RAVANELLI', 'REBELLO', 'REBELO', 'RECH', 'RECHIA', 'RECKELBERG', 'REINERT', 'REINGARDES', 'REIS', 'REITZ', 'RENGEL', 'RENKEN', 'RESNER', 'REZENDE', 'RIBAS', 'RIBEIRO', 'RIOS', 'RISKALLA', 'RITA', 'RÖB', 'ROBERGE', 'ROCHA', 'ROCHEI', 'ROCIO', 'RODE', 'RODERMEL', 'RÖDIG', 'RODRIGUES', 'RODRIGUES DA SILVA', 'RODRIGUEZ', 'ROES', 'ROHDEN', 'ROHLING', 'ROLIM', 'ROLING', 'ROMÃO', 'RONCALIO', 'RONCELLI', 'ROSA', 'ROSAR', 'ROSSI', 'ROTH', 'ROZA', 'RUBENS', 'RUBICK', 'RUIZ', 'RUSCH', 'RUZINSKY', 'SÁ', 'SAADE', 'SABEL', 'SABINO', 'SALETE', 'SALLES', 'SALOMONS', 'SALVADOR', 'SALVADORI', 'SANTIAGO', 'SANTINATO', 'SANTOS', 'SANTOS', 'SÃO JOSÉ', 'SARDÁ', 'SATTLERIN', 'SAUER', 'SAUSEN', 'SAX', 'SCARIONE', 'SCARPARO', 'SCHABO', 'SCHAEFER', 'SCHAEFFER', 'SCHÄFER', 'SCHÄFFER', 'SCHAPPO', 'SCHARF', 'SCHEFER', 'SCHEFFER', 'SCHEIDT', 'SCHEINBERG', 'SCHELL', 'SCHELLER', 'SCHERE', 'SCHERER', 'SCHIAPATI', 'SCHIESSLER', 'SCHIESTL', 'SCHIOCHET', 'SCHIOCHETT', 'SCHIRAUS', 'SCHIZATTI', 'SCHLEMPER', 'SCHLICKMANN', 'SCHLÖSSER', 'SCHLUPP', 'SCHMIDT', 'SCHMIT', 'SCHMITT', 'SCHMITZ', 'SCHMÖELLER', 'SCHMÖLIER', 'SCHMÖLLER', 'SCHNEIDER', 'SCHÖLL', 'SCHOLZE', 'SCHOTTEN', 'SCHREIBER', 'SCHRÖDER', 'SCHRÖEDER', 'SCHUCH', 'SCHUG', 'SCHUHMACHER', 'SCHULZ', 'SCHUMACHER', 'SCHUMANN', 'SCHÜRHAUS', 'SCHÜSLLER', 'SCHÜTZ', 'SCHÜTZE', 'SCHVAMBACH', 'SCHVARTZ', 'SCHWABE', 'SCHWAMBACH', 'SCHWARTZ', 'SCHWEITZER', 'SCHWEIZER', 'SCHWINDEN', 'SCRAMOCIN', 'SEBOLD', 'SEEMANN', 'SEGATA', 'SEHN', 'SEHNEM', 'SELHORST', 'SELL', 'SELLMAN', 'SELVA', 'SEMIANO', 'SENAGAGLIA', 'SENEM', 'SENS', 'SERAFIM', 'SEVERINO', 'SEZERINO', 'SHCERER', 'SIEBERT', 'SIEMENTCOSKI', 'SILVA', 'SILVEIRA', 'SILVÉRIO', 'SIMAS', 'SIMONES', 'SIPRIANI', 'SOARES', 'SOENS', 'SOFKA', 'SOLEK', 'SOMMER', 'SÖNS', 'SOTELI', 'SOUSA', 'SOUZA', 'SPRINGMANN', 'STACHINSKI', 'STADNICK', 'STÄHELIN', 'STALOCH', 'STAROSCKY', 'STAROSKY', 'STEFFEN', 'STEFFENS', 'STEIN', 'STEINBACH', 'STERN', 'STHEINHAUSEN', 'STIPP', 'STOCK', 'STOLF', 'STOPPAZZOLLI', 'STRAUB', 'STREY', 'STUART', 'STUEHLER', 'STÜEPP', 'STÜPP', 'SUELI', 'SUERTTEGARAY', 'SYRINO', 'TAMANINI', 'TAMBOSE', 'TARUHN', 'TEIXEIRA', 'TEJADA', 'TELES', 'TENFEN', 'TERLINDEN', 'TERRUEL', 'TEXEIRA', 'THADEO', 'THEISS', 'THIESEN', 'THIEVES', 'THIVES', 'THOL', 'TIECKARKA', 'TIMMLER', 'TOLL', 'TOMAZINE', 'TOMAZINHO', 'TOMAZONI', 'TOMBOSI', 'TONETT', 'TORETTA', 'TRAMONTIN', 'TRENTINI', 'TRIERVEILER', 'TRIERWEILER', 'TRIERWEILLER', 'TRISTÃO', 'TROCATTI', 'TRONCON', 'TRUPPEL', 'TURNÊS', 'TUSCHINSKI', 'UCHOA', 'UHLMANN', 'UNBEHAUN', 'UNTEMBERG', 'VALE', 'VALENTE', 'VALER', 'VALÉRIO', 'VALIM', 'VALZACK', 'VAMBÖMMEL', 'VANDERLINDE', 'VAN DER VINNE', 'VANDRESEN', 'VARGAS', 'VARMELIN', 'VARMELING', 'VASCONCELLOS', 'VASCONCELOS', 'VAVASSORI', 'VAZ', 'VEBER', 'VECHIA', 'VEGINI', 'VELASCO', 'VELHO', 'VENDRAMIN', 'VENTURA', 'VENTURI', 'VERMÖHLEN', 'VERTELI', 'VESELY', 'VIEIRA', 'VIGER', 'VILPERT', 'VILVERT', 'VINCE', 'VINTER', 'VITORETE', 'VOLPI', 'VOLTOLINI', 'VOSS', 'VOYTENA', 'WACHHOLZ', 'WAGNER', 'WALDRCH', 'WALDRICH', 'WALLESER', 'WALTER', 'WALTERSCHEID', 'WAMSER', 'WANDRESEN', 'WANROO', 'WARMLING', 'WASSEM', 'WASSEN', 'WEBBER', 'WEBER', 'WEIER', 'WEIGERT', 'WEIRICH', 'WEISER', 'WEISS', 'WELTER', 'WENDHAUSEN', 'WENTZ', 'WERLICH', 'WERMÖHLEN', 'WERNER', 'WERNKE', 'WESSEL', 'WESTPHAL', 'WESTRUP', 'WESTRUPP', 'WIDEMANN', 'WIESE', 'WIETHORN', 'WIGGERS', 'WILBERT', 'WILHELM', 'WILL', 'WILLEMANN', 'WILLVERT', 'WILWERT', 'WINTER', 'WIRCHEM', 'WIRSCCHEIN', 'WIRSCHEM', 'WLOCH', 'WOLF', 'WOLLINGER', 'WOLSKI', 'WOSS', 'WRUBLACK', 'WULFF', 'XAFRANSKY', 'XAVIER', 'YONGUE', 'ZANAN', 'ZANCANÁRIO', 'ZANELATO', 'ZANETTI', 'ZANIOLO', 'ZANON', 'ZANOTTI', 'ZEN', 'ZERMIANNI', 'ZEVEDO', 'ZIMERMANN', 'ZIMMEMANN', 'ZIMMERMANN', 'ZIRCHEN', 'ZOCCANTE', 'ZOZ', 'ZUNINO', 'ZVANG'];
    }

    public static function getRamosDeEmpresas(): array
    {
        return ['MECANICA', 'TECNOLOGIA', 'CONSTRUCAO', 'CONSULTORIA', 'ALIMENTICIA', 'COMERCIO', 'ENGENHARIA', 'ARQUITETURA', 'SAUDE', 'EDUCACAO', 'MARKETING', 'COMUNICACAO', 'DESIGN', 'FINANCEIRA', 'AUTOMOVEIS', 'IMOBILIARIA', 'AMBIENTAL', 'MODA', 'LOGISTICA', 'AGRICULTURA', 'ENERGIA', 'TRANSPORTES', 'TURISMO', 'ENTRETENIMENTO', 'QUIMICA', 'METALURGIA', 'VETERINARIA', 'FARMACEUTICA', 'ARTESANATO', 'TELECOMUNICACOES', 'BELEZA', 'PUBLICIDADE', 'NUTRICAO', 'ARTES', 'PERFUMARIA', 'ESPORTES', 'RECICLAGEM', 'EDITORIAL', 'ODONTOLOGIA', 'CONSULTORIO', 'ADVOCACIA', 'CONTABILIDADE', 'FISIOTERAPIA', 'JARDINAGEM', 'RESTAURANTE', 'PADARIA', 'ELETRONICA', 'MUSICA', 'ACADEMIA', 'FOTOGRAFIA', 'ARQUEOLOGIA', 'TURISMO', 'HOTELARIA', 'ESCRITORIO', 'TELECOMUNICACOES', 'ENGENHARIA CIVIL', 'ARQUITETURA', 'MODA', 'CONSTRUCAO', 'DECORACAO', 'GESTAO', 'MARKETING DIGITAL', 'TECNOLOGIA DA INFORMACAO', 'CONTABILIDADE', 'CONSULTORIA JURIDICA', 'SAUDE NATURAL', 'EDUCACAO ONLINE', 'ALIMENTOS ORGANICOS', 'PRODUCAO AUDIOVISUAL', 'ECOTURISMO', 'ENERGIAS RENOVAVEIS', 'ARTES MARCIAIS', 'ESTUDIO DE DESIGN', 'ESCRITORIO CRIATIVO', 'ARTE URBANA', 'TERAPIAS ALTERNATIVAS', 'BEM-ESTAR', 'ARQUITETURA SUSTENTAVEL', 'CONSULTORIA FINANCEIRA', 'TECNOLOGIA MEDICA'];
    }

    public static function getTiposDeEmpresas(): array
    {
        return ['LTDA', 'ME', 'SA', 'EIRELI'];
    }


}