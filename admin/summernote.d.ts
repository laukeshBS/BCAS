declare module 'jquery' {
  interface JQuery {
    summernote(options?: any): this; // Add summernote method
    summernote(method: string, ...args: any[]): any; // For method calls
  }
}
