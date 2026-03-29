# 💳 Payments

Biblioteca PHP para geração e manipulação de pagamentos, com suporte a layouts bancários (como CNAB240), voltada para integração com instituições financeiras.

## 📦 Sobre o projeto

Este projeto tem como objetivo facilitar a criação, processamento e integração de arquivos de remessa e retorno bancário, abstraindo regras específicas de cada banco e padronizando o uso dentro da aplicação.

Atualmente, inclui suporte para:

* Geração de arquivos CNAB240
* Integração com bancos (ex: Bradesco)
* Estrutura extensível para novos bancos
* Organização modular para facilitar manutenção

---

## 🚀 Instalação

Clone o repositório:

```bash
git clone https://github.com/edineivaldameri/payments.git
cd payments
```

Instale as dependências:

```bash
composer install
```

---

## ⚙️ Requisitos

* PHP 8.x
* Composer

---

## 📁 Estrutura do projeto

```text
src/
 └── Shipping/
     └── Cnab240/
         └── Bank/
             └── Bradesco.php
```

* `Shipping/Cnab240`: Implementação do layout CNAB240
* `Bank`: Classes específicas por banco

---

## 🧪 Testes

Para executar os testes:

```bash
vendor/bin/phpunit
```

---

## 🧹 Qualidade de código

O projeto utiliza ferramentas de análise estática e qualidade:

### PHPMD

```bash
vendor/bin/phpmd src ansi phpmd.xml
```

### PHPStan

```bash
vendor/bin/phpstan analyse
```

---

## 📌 Exemplo de uso

```php
use Payments\Shipping\Cnab240\Bank\Bradesco;

$bradesco = new Bradesco();

// Exemplo fictício
$arquivo = $bradesco->gerarRemessa($dados);

echo $arquivo;
```

> ⚠️ Ajuste conforme a implementação real dos métodos.

---

## 🔧 Personalização

Para adicionar suporte a novos bancos:

1. Crie uma nova classe em:

   ```
   src/Shipping/Cnab240/Bank/
   ```
2. Implemente as regras específicas
3. Siga o padrão das classes existentes

---

## 🤝 Contribuição

Contribuições são bem-vindas!

1. Fork o projeto
2. Crie uma branch:

   ```bash
   git checkout -b minha-feature
   ```
3. Commit suas alterações:

   ```bash
   git commit -m "Minha contribuição"
   ```
4. Push:

   ```bash
   git push origin minha-feature
   ```
5. Abra um Pull Request

---

## 📄 Licença

Este projeto está sob a licença MIT.

---

## 👨‍💻 Autor

Desenvolvido por **Edinei Alberton**

* GitHub: https://github.com/edineivaldameri

---

## 💡 Observações

Este projeto pode evoluir para suportar:

* CNAB400
* Múltiplos bancos
* Leitura de arquivos de retorno
* Validação de remessas

---
