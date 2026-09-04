<?php
namespace App\Services\Documentos;

use App\Models\Documento;
use App\Models\Folio;
use App\Models\TipoDocumento;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\DB;

class DocumentoService
{
    public function __construct(private readonly AuditService $audit) {}

    public function generarFolio(int $sedeId, int $tipoDocumentoId): string
    {
        return DB::transaction(function () use ($sedeId, $tipoDocumentoId) {
            $folio = \App\Models\Folio::lockForUpdate()->firstOrCreate(
                ['sede_id' => $sedeId, 'tipo_documento_id' => $tipoDocumentoId, 'anio' => now()->year],
                ['ultimo_numero' => 0]
            );
            $folio->increment('ultimo_numero');
            $folio->refresh();
            return sprintf('%d-%04d-%05d', $folio->anio, $tipoDocumentoId, $folio->ultimo_numero);
        });
    }

    public function crear(int $alumnoId, int $tipoId, int $sedeId, int $userId): Documento
    {
        $folio = $this->generarFolio($sedeId, $tipoId);
        $doc = Documento::create([
            'alumno_id'         => $alumnoId,
            'tipo_documento_id' => $tipoId,
            'sede_id'           => $sedeId,
            'folio'             => $folio,
            'version'           => 1,
            'estado'            => 'pendiente',
            'generado_por'      => $userId,
            'generado_at'       => now(),
        ]);
        $this->audit->log(modulo:'documentos', accion:'generate', model:Documento::class, modelId:$doc->id, descripcion:"Documento generado folio:{$folio}");
        return $doc;
    }

    public function autorizar(Documento $doc, int $userId): void
    {
        if ($doc->estado !== 'generado') throw new \RuntimeException('Solo se puede autorizar un documento generado.');
        $doc->update(['estado'=>'autorizado','autorizado_por'=>$userId,'autorizado_at'=>now()]);
        $this->audit->log(modulo:'documentos', accion:'authorize', model:Documento::class, modelId:$doc->id, before:['estado'=>'generado'], after:['estado'=>'autorizado']);
    }
}
