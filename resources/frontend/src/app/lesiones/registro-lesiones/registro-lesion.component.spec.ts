import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistroLesionesComponent } from './registro-lesion.component';

describe('RegistroLesionesComponent', () => {
  let component: RegistroLesionesComponent;
  let fixture: ComponentFixture<RegistroLesionesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ RegistroLesionesComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(RegistroLesionesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
