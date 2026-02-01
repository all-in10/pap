revise o sistema de login pois eu quero que ele use os roles para enviar o user para o seu panel de forma automatica independente do caminho de do login, para evita de situações onde o user entra na pagina de login do hr po acidente mas ele é um employee e faz o seu login eu quero adicionar um sistema de fallback e que veja o role e made o user para o seu panel

//
[{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureAdminPanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'panelPath'.",
	"source": "intelephense",
	"startLineNumber": 47,
	"startColumn": 32,
	"endLineNumber": 47,
	"endColumn": 41,
	"modelVersionId": 3,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureEmployeePanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'panelPath'.",
	"source": "intelephense",
	"startLineNumber": 43,
	"startColumn": 32,
	"endLineNumber": 43,
	"endColumn": 41,
	"modelVersionId": 3,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureHrPanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'panelPath'.",
	"source": "intelephense",
	"startLineNumber": 44,
	"startColumn": 32,
	"endLineNumber": 44,
	"endColumn": 41,
	"modelVersionId": 3,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/RedirectAuthenticatedToPanel.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'panelPath'.",
	"source": "intelephense",
	"startLineNumber": 21,
	"startColumn": 40,
	"endLineNumber": 21,
	"endColumn": 49,
	"modelVersionId": 32,
	"origin": "extHost1"
}]

//
ajuste a mensagem para informar o user dos requerimentos da pass

//
//
[{
	"resource": "/home/victor/pap/app/Http/Controllers/Filament/Hr/TimeoffApprovalController.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'authorize'.",
	"source": "intelephense",
	"startLineNumber": 15,
	"startColumn": 16,
	"endLineNumber": 15,
	"endColumn": 25,
	"modelVersionId": 34,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Controllers/Filament/Hr/TimeoffApprovalController.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'authorize'.",
	"source": "intelephense",
	"startLineNumber": 25,
	"startColumn": 16,
	"endLineNumber": 25,
	"endColumn": 25,
	"modelVersionId": 34,
	"origin": "extHost1"
}]

//w

Symfony\Component\Routing\Exception\RouteNotFoundException
vendor/laravel/framework/src/Illuminate/Routing/UrlGenerator.php:526
Route [filament.resources.timeoffs.view] not defined.

//w

Symfony\Component\Routing\Exception\RouteNotFoundException
vendor/laravel/framework/src/Illuminate/Routing/UrlGenerator.php:526
Route [filament.resources.timeoffs.edit] not defined.

//

Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php:131
The GET method is not supported for route livewire/update. Supported methods: POST.

//

isso acontece na paginação quando eu clico para ir para a segunda pagina, e refaça o widget para seguir a estetica dos widgets preexistentes

//
revise o autal sistema de login pois esta a haver instancias que eu user hr esta a ser  direcinado e tendo livre acesso ao panel dos admins e user admin sendo direcionados para o panel employee, e isso é uma falha crítica que não deve ir para a produção

//

crie um sistema de auditoria que tera como função fazer o log de tudo que acondete com os dados desde a criação, edição, e faça um log de toda vez que alguem tentar aceder a um panel sem o divido nivel de acesso

//

Isso apenas e unicamente o root tera acesso

//

aos logs crie um resouce que será only view 

//
resolve esses erros : [{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'user'.",
	"source": "intelephense",
	"startLineNumber": 23,
	"startColumn": 24,
	"endLineNumber": 23,
	"endColumn": 28,
	"modelVersionId": 52,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1038",
	"severity": 8,
	"message": "Method 'App\\Filament\\Resources\\AuditLogResource::table()' is not compatible with method 'Filament\\Resources\\Resource::table()'.",
	"source": "intelephense",
	"startLineNumber": 26,
	"startColumn": 5,
	"endLineNumber": 26,
	"endColumn": 54,
	"modelVersionId": 52,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Filament\\Resources\\Table'.",
	"source": "intelephense",
	"startLineNumber": 26,
	"startColumn": 34,
	"endLineNumber": 26,
	"endColumn": 39,
	"modelVersionId": 52,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Filament\\Resources\\Table'.",
	"source": "intelephense",
	"startLineNumber": 26,
	"startColumn": 49,
	"endLineNumber": 26,
	"endColumn": 54,
	"modelVersionId": 52,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1005",
	"severity": 8,
	"message": "Expected 1 arguments. Found 0.",
	"source": "intelephense",
	"startLineNumber": 33,
	"startColumn": 81,
	"endLineNumber": 33,
	"endColumn": 83,
	"modelVersionId": 52,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource/Pages/ListAuditLogs.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1038",
	"severity": 8,
	"message": "Method 'App\\Filament\\Resources\\AuditLogResource\\Pages\\ListAuditLogs::getTitle()' is not compatible with method 'Filament\\Resources\\Pages\\ListRecords::getTitle()'.",
	"source": "intelephense",
	"startLineNumber": 12,
	"startColumn": 5,
	"endLineNumber": 12,
	"endColumn": 42,
	"modelVersionId": 18,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource/Pages/ViewAuditLog.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1038",
	"severity": 8,
	"message": "Method 'App\\Filament\\Resources\\AuditLogResource\\Pages\\ViewAuditLog::getTitle()' is not compatible with method 'Filament\\Resources\\Pages\\ViewRecord::getTitle()'.",
	"source": "intelephense",
	"startLineNumber": 12,
	"startColumn": 5,
	"endLineNumber": 12,
	"endColumn": 42,
	"modelVersionId": 18,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureAdminPanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 27,
	"startColumn": 9,
	"endLineNumber": 27,
	"endColumn": 13,
	"modelVersionId": 33,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureAdminPanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 37,
	"startColumn": 13,
	"endLineNumber": 37,
	"endColumn": 17,
	"modelVersionId": 33,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureEmployeePanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 25,
	"startColumn": 9,
	"endLineNumber": 25,
	"endColumn": 13,
	"modelVersionId": 31,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureEmployeePanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 35,
	"startColumn": 13,
	"endLineNumber": 35,
	"endColumn": 17,
	"modelVersionId": 31,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureHrPanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 25,
	"startColumn": 9,
	"endLineNumber": 25,
	"endColumn": 13,
	"modelVersionId": 33,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Http/Middleware/EnsureHrPanelAccess.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 36,
	"startColumn": 13,
	"endLineNumber": 36,
	"endColumn": 17,
	"modelVersionId": 33,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Providers/AppServiceProvider.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 60,
	"startColumn": 17,
	"endLineNumber": 60,
	"endColumn": 21,
	"modelVersionId": 2,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Providers/AppServiceProvider.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 74,
	"startColumn": 17,
	"endLineNumber": 74,
	"endColumn": 21,
	"modelVersionId": 2,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Providers/AppServiceProvider.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1009",
	"severity": 8,
	"message": "Undefined type 'Log'.",
	"source": "intelephense",
	"startLineNumber": 87,
	"startColumn": 17,
	"endLineNumber": 87,
	"endColumn": 21,
	"modelVersionId": 2,
	"origin": "extHost1"
}]

//

[{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'user'.",
	"source": "intelephense",
	"startLineNumber": 23,
	"startColumn": 24,
	"endLineNumber": 23,
	"endColumn": 28,
	"modelVersionId": 53,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1005",
	"severity": 8,
	"message": "Expected 1 arguments. Found 0.",
	"source": "intelephense",
	"startLineNumber": 33,
	"startColumn": 81,
	"endLineNumber": 33,
	"endColumn": 83,
	"modelVersionId": 53,
	"origin": "extHost1"
}]

[{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'user'.",
	"source": "intelephense",
	"startLineNumber": 23,
	"startColumn": 24,
	"endLineNumber": 23,
	"endColumn": 28,
	"modelVersionId": 54,
	"origin": "extHost1"
}]

//

[{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'user'.",
	"source": "intelephense",
	"startLineNumber": 23,
	"startColumn": 24,
	"endLineNumber": 23,
	"endColumn": 28,
	"modelVersionId": 68,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#0",
	"severity": 8,
	"message": "syntax error, unexpected token \":\", expecting \"]\"",
	"startLineNumber": 33,
	"startColumn": 1,
	"endLineNumber": 33,
	"endColumn": 2147483648,
	"modelVersionId": 1,
	"origin": "extHost1"
},{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#0",
	"severity": 8,
	"message": "syntax error, unexpected token \":\", expecting \"]\"",
	"startLineNumber": 33,
	"startColumn": 1,
	"endLineNumber": 33,
	"endColumn": 2147483648,
	"modelVersionId": 1,
	"origin": "extHost1"
}]

//
[{
	"resource": "/home/victor/pap/app/Filament/Resources/AuditLogResource.php",
	"owner": "_generated_diagnostic_collection_name_#1",
	"code": "P1013",
	"severity": 8,
	"message": "Undefined method 'user'.",
	"source": "intelephense",
	"startLineNumber": 23,
	"startColumn": 24,
	"endLineNumber": 23,
	"endColumn": 28,
	"modelVersionId": 75,
	"origin": "extHost1"
}]

//
atualize o summary para refetir o estado atual da aplicação