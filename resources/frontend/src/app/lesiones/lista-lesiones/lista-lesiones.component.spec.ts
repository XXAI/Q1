import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListaLesionesComponent } from './lista-lesiones.component';

describe('ListComponent', () => {
  let component: ListaLesionesComponent;
  let fixture: ComponentFixture<ListaLesionesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListaLesionesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListaLesionesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
