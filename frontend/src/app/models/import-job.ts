import { ImportJobError } from './import-job-error';

export interface ImportJob {
  id: number;
  originalFilename: string;
  status:
    | 'queued'
    | 'processing'
    | 'completed'
    | 'completed_with_errors'
    | 'failed';
  totalRows: number;
  processedRows: number;
  successfulRows: number;
  failedRows: number;
  createdAt: string;
  updatedAt: string;
  finishedAt: string | null;

  errors: ImportJobError[];
}
