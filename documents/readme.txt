Orange - PHP libraries
	+- 1. FormGenerator
		+- 1.1 Configs
		+- 1.2 FormElement
		+- 1.3 FormGeneratorException
		+- 1.4 Patterns
		+- 1.5 Validation
	+- 2. Vendors
		+- 2.1 Symfony
			+- 2.1.1 Component
				+- 2.1.1.1 ClassLoader
				+- 2.1.1.2 Yaml
				
Resources:
	+- 3. js
	+- 4. css
	
--------------------------------------------------------
1. FormGenerator
Files:
FormGenerator.php
Classe "Wrapper" que cria, analisa e valida formulários. Responsável para administrar cache e faz o output do nosso formulário.
--------------------------------------------------------
1.1 configs
Files:
*.yaml
Ficheiro de configuração do formulário, validação e elementos do formulário
*.html
Ficheiro template do formulário
--------------------------------------------------------
1.2 FormElement
Files:
BaseElement.php
Base de todos os elementos html do formulário, inputs, form, select, option, label etc.. Trata e faz parse do html e attributos
InputElement.php
Base dos elementos do tipo input
FormElement.php
Base da tag form
InterfaceElement.php
Interface implmentado para manter o nível de abstração na implmentação dos elementos no FormGenerator.php
--------------------------------------------------------
1.3 FormGeneratorException
Files:
~ A implmentar
--------------------------------------------------------
1.4 Patterns
Files:
ElementFactory.php
Inicializa objectos do tipo BaseElement
--------------------------------------------------------
1.5 Validation
Files:
~ A implementar
--------------------------------------------------------

TODO

--------------------------------------------------------
Geração de cache do html + php do form, valida a cache se o ficheiro de configuração foi alterado
Atribuição de valores post em variáveis pré inicializadas no início do script gerado
GoupElements : para groupos de options, checkbox ou radio
Validation: Toda a hieraquia das classes de validação - Geram script js no plugin de validação
Método estático que guarda info necessária para avaliar se o isValid() é true ou false (Atenção o isvalid tem que ser estático)
Serialização dos dados do form para a sessão.
Métodos clearValues, clearError e ReadOnly no formGenerator
PHPDocs, diagramas de classe e sequencia.
Muito mais a ser considerado